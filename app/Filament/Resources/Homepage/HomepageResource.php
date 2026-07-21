<?php

namespace App\Filament\Resources\Homepage;

use App\Filament\Resources\Homepage\Pages\EditHomepage;
use App\Filament\Resources\Homepage\Schemas\HomepageForm;
use App\Models\Homepage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class HomepageResource extends Resource
{
    protected static ?string $model = Homepage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Homepage';

    protected static ?string $breadcrumb = 'Homepage';

    public static function form(Schema $schema): Schema
    {
        return HomepageForm::configure($schema);
    }

    public static function getPages(): array
    {
        // Only an edit page – no list, no create.
        // The navigation link goes directly to the edit form.
        return [
            'index' => EditHomepage::route('/'),
        ];
    }

    /**
     * Point navigation straight to the singleton edit page.
     */
    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }
}

