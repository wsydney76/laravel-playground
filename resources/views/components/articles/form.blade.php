@use('App\Enums\State')
@use('App\Enums\Locale')

@props([
    'action',
    'method' => 'POST',
    'article' => null,
    'submitLabel',
    'cancelHref',
])

@php
    use App\Models\Article;
    /** @var string $action */
    /** @var string $method */
    /** @var Article|null $article */
    /** @var string $submitLabel */
    /** @var string $cancelHref */
@endphp

<form method="POST" class="max-w-3xl" action="{{ $action }}" enctype="multipart/form-data">
    @if ($errors->any())
        <flux:callout variant="danger" class="mb-6">
            <flux:callout.heading>
                {{ __('There were some problems with your input.') }}
            </flux:callout.heading>
            <flux:callout.text>
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </flux:callout.text>
        </flux:callout>
    @endif

    @csrf
    @if (! in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endif

    <flux:card class="mt-2 bg-sky-100 p-2 dark:border-sky-700 dark:bg-sky-900">
        <flux:tab.group>
            <flux:tabs>
                @foreach (Locale::cases() as $locale)
                    <flux:tab :name="$locale->value">{{ $locale->label() }}</flux:tab>
                @endforeach
            </flux:tabs>

            @foreach (Locale::cases() as $locale)
                <flux:tab.panel :name="$locale->value" class="space-y-6">
                    <div>
                        <flux:input
                            :label="__('Title') .' (' . $locale->label() . ')'"
                            name="title[{{ $locale->value }}]"
                            id="article-title-{{ $locale->value }}"
                            value="{{ old('title.' . $locale->value, $article?->getTranslation('title', $locale->value)) }}"
                            autofocus
                        />

                        <flux:error name="title.{{ $locale->value }}" />
                        <x-shared.copy-value id="article-title" :locale="$locale" />
                    </div>

                    <div>
                        <flux:input
                            :label="__('Slug') .' (' . $locale->label() . ')'"
                            name="slug[{{ $locale->value }}]"
                            id="article-slug-{{ $locale->value }}"
                            value="{{ old('slug.' . $locale->value, $article?->getTranslation('slug', $locale->value)) }}"
                            :placeholder="__('a valid slug (optional)')"
                            :description:trailing="__('If left empty, a slug will be automatically generated from the title.')"
                        />

                        <flux:error name="slug.{{ $locale->value }}" />
                    </div>

                    <div>
                        <flux:textarea
                            :label="__('Body') .' (' . $locale->label() . ')'"
                            name="body[{{ $locale->value }}]"
                            id="article-body-{{ $locale->value }}"
                            rows="8"
                        >
                            {{ old('body.' . $locale->value, $article?->getTranslation('body', $locale->value)) }}


                        </flux:textarea>
                        <flux:error name="body.{{ $locale->value }}" />

                        <x-shared.copy-value id="article-body" :locale="$locale" />
                    </div>
                </flux:tab.panel>
            @endforeach
        </flux:tab.group>
    </flux:card>
    <div class="mb-6 grid grid-cols-2 gap-6">
        <div>
            <flux:select
                :label="__('State')"
                name="state"
                id="article-state"
                value="{{ old('state', $article?->state?->value) }}"
            >
                @foreach (State::cases() as $state)
                    <flux:select.option
                        value="{{ $state->value }}"
                        :selected="old('state', $article?->state?->value) === $state->value"
                    >
                        {{ $state->label() }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <x-image-upload
        :model="$article"
        collection="featured_image"
        name="featured_image"
        :label="__('Featured Image')"
    />

    <div class="mt-8 flex gap-3">
        <flux:button size="sm" variant="primary" color="sky" type="submit">
            {{ $submitLabel }}
        </flux:button>
        <flux:button size="sm" as="a" variant="ghost" href="{{ $cancelHref }}">
            {{ __('Cancel') }}
        </flux:button>
    </div>
</form>

<x-scripts.beforeunload />
