<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ФИО врача')
                    ->required(),
            ]);
    }
}
