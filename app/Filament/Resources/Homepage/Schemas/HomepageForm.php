<?php

namespace App\Filament\Resources\Homepage\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HomepageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sitename')
                    ->required()
                    ->maxLength(255),
                TextInput::make('copyright')
                    ->required()
                    ->maxLength(255),
                Textarea::make('homepagetext')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

