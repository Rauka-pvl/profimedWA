<?php

namespace App\Filament\Resources\Appointments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('doctor_id')
                    ->label('ФИО врача')
                    ->required(),
                TextInput::make('patient_id')
                    ->required()
                    ->label('ФИО пациента'),
                TextInput::make('service')
                    ->label('Услуга')
                    ->required(),
                TextInput::make('cabinet')
                    ->label('Кабинет')
                    ->required()
                    ->numeric(),
                DatePicker::make('date')
                    ->label('Дата записи')
                    ->required(),
                TimePicker::make('time')
                    ->label('Время записи')
                    ->required(),
                Select::make('status')
                    ->label('Статус')
                    ->options(['scheduled' => 'Записан', 'completed' => 'Выполнил', 'cancelled' => 'Отменён'])
                    ->default('scheduled')
                    ->required(),
            ]);
    }
}
