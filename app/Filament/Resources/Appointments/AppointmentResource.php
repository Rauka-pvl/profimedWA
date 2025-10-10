<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use BackedEnum;
use Carbon\Carbon;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = 'iconpark-appointment-o';

    protected static ?string $recordTitleAttribute = 'Записи';
    protected static ?string $modelLabel = 'Запись';
    protected static ?string $pluralModelLabel = 'Записи';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        // return AppointmentForm::configure($schema);
        return $schema
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->label('Врач')
                    ->relationship('doctor', 'name') // 👈 связь doctor() в модели Appointment
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('patient_id')
                    ->label('Пациент')
                    ->relationship('patient', 'full_name') // 👈 связь patient() в модели Appointment
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Дата приёма')
                    ->required(),

                Forms\Components\TextInput::make('time')
                    ->label('Время')
                    ->placeholder('Например: 14:30 - 14:45')
                    ->required(),

                Forms\Components\TextInput::make('cabinet')
                    ->label('Кабинет'),

                Forms\Components\TextInput::make('service')
                    ->label('Услуга'),

                Forms\Components\Select::make('status')
                    ->label('Статус')
                    ->options([
                        'scheduled' => 'Записан',
                        'completed' => 'Выполнен',
                        'cancelled' => 'Отменён',
                    ])
                    ->default('scheduled')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}
