<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Models\Doctor;
use App\Models\Patient;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                    ->label('Врач')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('patient.full_name')
                    ->label('Пациент')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('service')
                    ->label('Услуга')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cabinet')
                    ->label('Кабинет')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('date')
                    ->label('Дата записи')
                    ->date()
                    ->sortable(),

                TextColumn::make('time')
                    ->label('Время записи')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'scheduled' => 'Записан',
                        'completed' => 'Выполнен',
                        'cancelled' => 'Отменён',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('reminder_3h_sent')
                    ->label('Напоминание за 3 часа')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '0' => 'Нет',
                        '1' => 'Да',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('reminder_24h_sent')
                    ->label('Напоминание за 24 часа')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '0' => 'Нет',
                        '1' => 'Да',
                        default => ucfirst($state),
                    })
                    ->color(fn(string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                        default => 'gray',
                    }),

            ])

            ->filters([
                // 🔹 фильтр по врачу
                SelectFilter::make('doctor_id')
                    ->label('Врач')
                    ->options(fn() => Doctor::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),

                // 🔹 фильтр по пациенту
                SelectFilter::make('patient_id')
                    ->label('Пациент')
                    ->options(fn() => Patient::orderBy('full_name')->pluck('full_name', 'id'))
                    ->searchable(),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
