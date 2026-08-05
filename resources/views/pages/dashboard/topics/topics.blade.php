<div>
    <x-layouts::dashboard
        :heading="__('Topics')"
        :subheading="__('Create, update, and delete topics')"
    >
        <div class="mb-4 flex justify-end">
            <flux:button
                variant="primary"
                color="sky"
                size="sm"
                icon="plus"
                x-on:click="$flux.modal('create-topic').show()"
            >
                {{ __('New Topic') }}
            </flux:button>
        </div>

        @if ($this->topics->isEmpty())
            <flux:callout variant="warning">
                <flux:callout.heading>{{ __('No topics yet') }}</flux:callout.heading>
                <flux:callout.text>{{ __('Create your first topic using the button above.') }}</flux:callout.text>
            </flux:callout>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Image') }}</flux:table.column>
                    <flux:table.column>{{ __('Title') }}</flux:table.column>
                    <flux:table.column>{{ __('Color') }}</flux:table.column>
                    <flux:table.column>{{ __('Articles') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                @foreach ($this->topics as $topic)
                    <flux:table.row wire:key="topic-{{ $topic->id }}">
                        <flux:table.cell class="w-16">
                            @if ($imageUrl = $topic->featured_image_url)
                                <img src="{{ $imageUrl }}" alt="{{ $topic->title }}" class="h-10 w-16 rounded object-cover" />
                            @else
                                <div class="h-10 w-16 rounded bg-zinc-200 dark:bg-zinc-700"></div>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:text class="font-medium">{{ $topic->title }}</flux:text>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge :color="$topic->color->value" size="sm">{{ $topic->color->label() }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge>{{ $topic->articles_count }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex gap-2">
                                <flux:button
                                    size="xs"
                                    variant="filled"
                                    wire:click="openEdit({{ $topic->id }})"
                                >
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button
                                    size="xs"
                                    variant="danger"
                                    wire:click="deleteTopic({{ $topic->id }})"
                                    wire:confirm="{{ __('Delete this topic? It will be removed from all articles.') }}"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table>
        @endif

        {{-- Create Modal --}}
        <flux:modal name="create-topic" class="w-full max-w-md space-y-6">
            <flux:heading>{{ __('New Topic') }}</flux:heading>

            <form wire:submit="createTopic" class="space-y-4">
                <flux:input
                    wire:model="newTitle.en"
                    :label="__('Title (EN)')"
                    :placeholder="__('Topic title in English')"
                    autofocus
                />
                <flux:error name="newTitle.en" />

                <flux:input
                    wire:model="newSlug.en"
                    :label="__('Slug (EN)')"
                    :placeholder="__('a valid slug (optional)')"
                    :description:trailing="__('If left empty, a slug will be automatically generated from the title.')"
                />
                <flux:error name="newSlug.en" />

                <flux:input
                    wire:model="newTitle.de"
                    :label="__('Title (DE)')"
                    :placeholder="__('Topic title in German')"
                />
                <flux:error name="newTitle.de" />

                <flux:input
                    wire:model="newSlug.de"
                    :label="__('Slug (DE)')"
                    :placeholder="__('a valid slug (optional)')"
                    :description:trailing="__('If left empty, a slug will be automatically generated from the title.')"
                />
                <flux:error name="newSlug.de" />

                <div>
                    <flux:select wire:model.live="newColor" :label="__('Color')">
                        @foreach ($this->colors as $color)
                            <flux:select.option value="{{ $color->value }}">{{ $color->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="newColor" />
                    <div class="mt-2">
                        <flux:badge :color="$newColor" size="sm">{{ __('Preview') }}</flux:badge>
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Featured Image') }}</flux:label>
                    <flux:file-upload wire:model="newImage" :label="__('Upload image')" class="mt-1">
                        <flux:file-upload.dropzone
                            heading="Drop image here or click to browse"
                            text="JPG, PNG, WebP"
                            inline
                            with-progress
                        />
                    </flux:file-upload>
                    @if ($newImage)
                        <img src="{{ $newImage->temporaryUrl() }}" class="mt-2 h-24 w-full rounded object-cover" />
                    @endif
                    <flux:error name="newImage" />
                </div>

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary" color="sky">{{ __('Create') }}</flux:button>
                    <flux:button x-on:click="$flux.modal('create-topic').close()" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- Edit Modal --}}
        <flux:modal name="edit-topic" class="w-full max-w-md space-y-6">
            <flux:heading>{{ __('Edit Topic') }}</flux:heading>

            <form wire:submit="updateTopic" class="space-y-4">
                <flux:input
                    wire:model="editTitle.en"
                    :label="__('Title (EN)')"
                    autofocus
                />
                <flux:error name="editTitle.en" />

                <flux:input
                    wire:model="editSlug.en"
                    :label="__('Slug (EN)')"
                    :placeholder="__('a valid slug (optional)')"
                    :description:trailing="__('If left empty, a slug will be automatically generated from the title.')"
                />
                <flux:error name="editSlug.en" />

                <flux:input
                    wire:model="editTitle.de"
                    :label="__('Title (DE)')"
                />
                <flux:error name="editTitle.de" />

                <flux:input
                    wire:model="editSlug.de"
                    :label="__('Slug (DE)')"
                    :placeholder="__('a valid slug (optional)')"
                    :description:trailing="__('If left empty, a slug will be automatically generated from the title.')"
                />
                <flux:error name="editSlug.de" />

                <div>
                    <flux:select wire:model.live="editColor" :label="__('Color')">
                        @foreach ($this->colors as $color)
                            <flux:select.option value="{{ $color->value }}">{{ $color->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="editColor" />
                    <div class="mt-2">
                        <flux:badge :color="$editColor" size="sm">{{ __('Preview') }}</flux:badge>
                    </div>
                </div>

                <div>
                    <flux:label>{{ __('Featured Image') }}</flux:label>
                    <flux:file-upload wire:model="editImage" :label="__('Upload new image')" class="mt-1">
                        <flux:file-upload.dropzone
                            heading="Drop image here or click to browse"
                            text="JPG, PNG, WebP"
                            inline
                            with-progress
                        />
                    </flux:file-upload>
                    @if ($editImage)
                        <img src="{{ $editImage->temporaryUrl() }}" class="mt-2 h-24 w-full rounded object-cover" />
                    @endif
                    <flux:error name="editImage" />
                </div>

                @php $editTopic = $editingId ? \App\Models\Topic::find($editingId) : null; @endphp
                @if ($editTopic?->featured_image_url)
                    <div>
                        <flux:label>{{ __('Current image') }}</flux:label>
                        <img src="{{ $editTopic->featured_image_url }}" class="mt-1 h-24 w-full rounded object-cover" />
                        <flux:checkbox
                            wire:model="deleteEditImage"
                            :label="__('Delete current image')"
                            class="mt-2"
                        />
                    </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <flux:button type="submit" variant="primary" color="sky">{{ __('Save') }}</flux:button>
                    <flux:button x-on:click="$flux.modal('edit-topic').close()" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            </form>
        </flux:modal>
    </x-layouts::dashboard>
</div>

