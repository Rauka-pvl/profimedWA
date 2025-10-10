<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AppointmentsChart extends ChartWidget
{
    protected ?string $heading = 'Приёмы за последние 7 дней';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d.m');
            $data[] = Appointment::whereDate('date', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Количество приёмов',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
        // return 'bubble';
    }
}
