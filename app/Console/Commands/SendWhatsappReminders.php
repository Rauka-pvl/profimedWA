<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Отправка напоминаний в WhatsApp за 24ч и 3ч до приёма (с допуском ±15 минут)';

    public function handle()
    {
        $now = Carbon::now();

        // 📅 Напоминания за 24 часа
        $target24 = $now->copy()->addDay();
        $this->sendReminders($target24, 'reminder_24h_sent', 24);

        // ⏰ Напоминания за 3 часа
        $target3 = $now->copy()->addHours(3);
        $this->sendReminders($target3, 'reminder_3h_sent', 3);

        return Command::SUCCESS;
    }

    protected function sendReminders(Carbon $targetTime, string $flagField, int $hours)
    {
        $startWindow = $targetTime->copy()->subMinutes(15);
        $endWindow = $targetTime->copy()->addMinutes(15);

        // ⚙️ Берём приёмы, у которых время в пределах ±15 минут
        $appointments = Appointment::where('status', 'scheduled')
            ->where($flagField, false)
            ->whereBetween('date', [
                $startWindow->copy()->toDateString(),
                $endWindow->copy()->toDateString(),
            ])
            ->with('patient', 'doctor')
            ->get()
            ->filter(function ($appointment) use ($startWindow, $endWindow) {
                // сравнение по времени
                $appointmentDateTime = Carbon::parse("{$appointment->date} " . substr($appointment->time, 0, 5));
                return $appointmentDateTime->between($startWindow, $endWindow);
            });

        if ($appointments->isEmpty()) {
            $this->info("⏳ Нет записей для напоминания за {$hours}ч");
            return;
        }

        foreach ($appointments as $appointment) {
            if (!$appointment->patient?->phone) continue;
            if ($appointment->status === 'cancelled') continue;

            $phone = preg_replace('/\D+/', '', $appointment->patient->phone);
            $message = "Здравствуйте, {$appointment->patient->full_name}!
Напоминаем, что у вас запись к врачу {$appointment->doctor->name}
на {$appointment->date} в {$appointment->time} ({$appointment->service}).";

            $this->sendWhatsAppMessage($phone, $message);

            $appointment->$flagField = true;
            $appointment->save();

            $this->info("✅ Отправлено напоминание {$hours}ч → {$appointment->patient->full_name}");
        }
    }

    protected function sendWhatsAppMessage(string $phone, string $message)
    {
        $url = "https://7105.api.green-api.com/waInstance7105339334/SendMessage/f0317185b97246c6bba0d986105e50af8fa7986db8804c74a6";

        $payload = [
            "chatId" => "77052942081@c.us",
            "message" => $message,
        ];

        try {
            $response = Http::timeout(20)->post($url, $payload);

            if ($response->failed()) {
                Log::error('❌ Ошибка Green API', [
                    'phone' => $phone,
                    'response' => $response->body(),
                ]);
            } else {
                Log::info('📨 Сообщение отправлено', ['phone' => $phone]);
            }
        } catch (\Exception $e) {
            Log::error('🚨 Ошибка отправки в WhatsApp', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
