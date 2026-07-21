<?php

use App\Models\Homepage;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dashboard - Homepage')] class extends Component {
    public string $sitename = '';
    public string $copyright = '';
    public string $homepagetext = '';

    public function mount(): void
    {
        $homepage = Homepage::getSingleton();
        $this->authorize('update', $homepage);

        $this->sitename = $homepage->sitename;
        $this->copyright = $homepage->copyright;
        $this->homepagetext = $homepage->homepagetext;
    }

    public function update(): void
    {
        $homepage = Homepage::getSingleton();
        $this->authorize('update', $homepage);

        $validated = $this->validate([
            'sitename' => ['required', 'string', 'max:255'],
            'homepagetext' => ['required', 'string'],
            'copyright' => ['required', 'string', 'max:255'],
        ]);

        $homepage->update($validated);

        $this->dispatch('homepage-updated');
    }
};
?>

<div>
    <x-layouts::dashboard
        :heading="__('Homepage')"
        :subheading="__('Manage the homepage content.')"
    >
        <flux:card class="p-6">
            <flux:text>
                {{ __('Logo will be delivered from asset path :file', ['file' => config('app.logo')]) }}
            </flux:text>

            <form wire:submit="update" class="mt-6 space-y-6">
                <flux:input wire:model="sitename" :label="__('Site Name')" type="text" />

                <flux:textarea wire:model="homepagetext" :label="__('Homepage Text')" rows="10" />

                <flux:input wire:model="copyright" :label="__('Copyright')" type="text" />

                <div class="flex items-center gap-4">
                    <flux:button variant="primary" type="submit">
                        {{ __('Save') }}
                    </flux:button>

                    <x-auth.action-message on="homepage-updated">
                        {{ __('Saved.') }}
                    </x-auth.action-message>
                </div>
            </form>
        </flux:card>
    </x-layouts::dashboard>
</div>
