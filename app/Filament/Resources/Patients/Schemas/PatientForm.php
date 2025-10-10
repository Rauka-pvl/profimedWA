<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
            ]);
    }
}
