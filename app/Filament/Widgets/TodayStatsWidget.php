<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class TodayStatsWidget extends StatsOverviewWidget
{
    // protected function getStats(): array
    // {
    //     return [
    //         //
    //     ];
    // }
    protected function getCards(): array
    {
        $today = Carbon::today();

        return [
            Stat::make('Приёмов сегодня', Appointment::whereDate('date', $today)->count())
                ->description('Всего записей на ' . $today->format('d.m.Y'))
                ->icon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Выполнено', Appointment::whereDate('date', $today)->where('status', 'completed')->count())
                ->description('Приёмы закрыты')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Отменено', Appointment::whereDate('date', $today)->where('status', 'cancelled')->count())
                ->description('Не состоялись')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Новых пациентов', Patient::whereDate('created_at', $today)->count())
                ->description('Добавлено сегодня')
                ->icon('heroicon-o-user-plus')
                ->color('warning'),
        ];
    }
}
