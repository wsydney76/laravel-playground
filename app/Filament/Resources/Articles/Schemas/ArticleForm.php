<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')->relationship('user', 'name')->required(),
            Select::make('creator_id')->relationship('creator', 'name')->required(),
            TextInput::make('title.en')->label('Title en')->required(),
            TextInput::make('title.de')->label('Title de')->required(),
            TextInput::make('slug.en')->label('Slug en')->required(),
            TextInput::make('slug.de')->label('Slug de')->required(),
            Textarea::make('body.de')->label('Body en')->required()->columnSpanFull(),
            Textarea::make('body.en')->label('Body de')->required()->columnSpanFull(),
            Select::make('state')->options(State::class)->default('published')->required(),
        ]);
    }
}
