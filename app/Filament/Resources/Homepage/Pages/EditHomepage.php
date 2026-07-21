<?php

namespace App\Filament\Resources\Homepage\Pages;

use App\Filament\Resources\Homepage\HomepageResource;
use App\Models\Homepage;
use Filament\Resources\Pages\EditRecord;

class EditHomepage extends EditRecord
{
    protected static string $resource = HomepageResource::class;

    /**
     * Override mount so no {record} route parameter is needed.
     * We always load the singleton Homepage record directly.
     */
    public function mount(int|string|null $record = null): void
    {
        $this->record = Homepage::getSingleton();

        $this->authorizeAccess();
        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function getHeaderActions(): array
    {
        // No delete action – the singleton must never be deleted via the UI.
        return [];
    }
}

