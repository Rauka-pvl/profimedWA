<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class UploadPdfWidget extends Widget implements HasForms
{
    use InteractsWithForms;
    public $pdf = null;
    protected string $view = 'filament.widgets.upload-pdf-widget';

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('pdf')
                ->label('Загрузить PDF файл')
                ->directory('uploads/pdfs')
                ->acceptedFileTypes(['application/pdf'])
                ->required(),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();
        $file = $data['pdf'] ?? null;

        if (!$file) {
            Notification::make()
                ->title('Ошибка')
                ->body('Выберите PDF файл.')
                ->danger()
                ->send();
            return;
        }

        $path = storage_path('app/private/' . $file);

        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        // --- Разбиваем на блоки по врачам ---
        $blocks = preg_split('/(?=\d{2}\.\d{2}\.\d{4}\s+[А-ЯЁA-Z][а-яёa-z]+)/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        $importedCount = 0;

        foreach ($blocks as $block) {
            // --- Ищем дату и врача ---
            if (preg_match('/(\d{2}\.\d{2}\.\d{4})\s+([А-ЯЁA-ZЁӘІҢҒҮҰҚӨҺ][а-яёa-zәіңғүұқөһ]+(?:\s+[А-ЯЁA-ZЁӘІҢҒҮҰҚӨҺ][а-яёa-zәіңғүұқөһ]+){0,2})/u', $block, $m)) {
                $date = date('Y-m-d', strtotime(str_replace('.', '-', $m[1])));
                $doctorName = trim($m[2]);
            } else {
                continue;
            }

            // --- Ищем приёмы ---
            preg_match_all(
                '/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})\s*(.*?)\((.*?)\)\s*([А-ЯЁA-ZЁӘІҢҒҮҰҚӨҺ][^+]+)\+?\s*([+]?\d[\d\s\-()]{7,})?\s*(.+?)(?=(?:\d{2}:\d{2}\s*-\s*\d{2}:\d{2}|Всего приемов|$))/su',
                $block,
                $matches,
                PREG_SET_ORDER
            );

            $doctor = Doctor::firstOrCreate(['name' => $doctorName]);

            foreach ($matches as $m) {
                $start = trim($m[1]);
                $end = trim($m[2]);
                $time = "{$start} - {$end}";

                $cabinet = trim($m[4] ?? '');
                $patientName = trim(preg_replace("/\s+/", ' ', $m[5]));

                // 🔹 Извлекаем все номера телефонов (могут быть через запятую, /, пробел)
                preg_match_all('/(\+?\d[\d\s\-()]{7,})/u', $m[0], $phones);
                $phones = array_map(fn($p) => preg_replace('/\D+/', '', $p), $phones[1] ?? []);

                // 🔹 Основной номер — первый
                $primaryPhone = $phones[0] ?? null;

                // 🔹 Все номера — строкой
                $allPhones = implode(', ', array_unique($phones));

                $service = trim(preg_replace("/\s+/", ' ', $m[7]));

                if (!$patientName) continue;

                // 🔹 Если в БД телефона нет — обновим
                $patient = Patient::firstOrCreate(
                    ['full_name' => $patientName],
                    ['phone' => $primaryPhone ?? '']
                );

                // Если у пациента пустой телефон, но в новом файле есть — обновим
                if (!$patient->phone && $primaryPhone) {
                    $patient->update(['phone' => $primaryPhone]);
                }

                Appointment::updateOrCreate([
                    'doctor_id' => $doctor->id,
                    'patient_id' => $patient->id,
                    'date' => $date,
                    'time' => $time,
                ], [
                    'service' => $service ?: 'Не указано',
                    'cabinet' => $cabinet ?: '',
                    'status' => 'scheduled',
                ]);

                $importedCount++;
            }
        }

        Notification::make()
            ->title('Импорт завершён')
            ->body("Импортировано {$importedCount} записей.")
            ->success()
            ->send();

        $this->form->fill();
    }
}
