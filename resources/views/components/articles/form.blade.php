@props([
    'action',
    'method' => 'POST',
    'article' => null,
    'submitLabel',
    'cancelHref',
])

<form method="POST" class="max-w-3xl" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if (! in_array(strtoupper($method), ['GET', 'POST']))
        @method($method)
    @endif

    <div class="mb-6">
        <flux:input
            :label="__('Title')"
            name="title"
            id="article-title"
            value="{{ old('title', $article?->title) }}"
            autofocus
        />
    </div>

    <div class="mb-6">
        <flux:input
            :label="__('Slug')"
            name="slug"
            id="article-slug"
            value="{{ old('slug', $article?->slug) }}"
            :placeholder="__('a valid slug (optional)')"
            :description:trailing="__('If left empty, a slug will be automatically generated from the title.')"
        />
    </div>

    <div class="mb-6">
        <flux:textarea :label="__('Body')" name="body" rows="8">
            {{ old('body', $article?->body) }}
        </flux:textarea>
        <flux:error name="body" />
    </div>

    <flux:label>{{ __('Featured Image') }}</flux:label>

    <flux:card class="mt-2">
        <div class="flex items-start gap-8">
            @if ($article?->hasMedia('featured_image'))
                <div>
                    <img
                        src="{{ $article->getFirstMediaUrl('featured_image', 'thumb') }}"
                        alt="{{ __('Current featured image') }}"
                        class="h-32 w-auto rounded-md object-cover"
                    />
                </div>
            @endif

            <div>
                <flux:input
                    id="featured_image"
                    name="featured_image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                />
                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @if ($article?->hasMedia('featured_image'))
                <div class="mt-2">
                    <flux:checkbox
                        :label="__('Delete current image')"
                        name="delete_featured_image"
                        value="1"
                    />
                    <p class="mt-2 text-xs text-zinc-400">
                        {{ __('Or upload a new image to replace it.') }}
                    </p>
                </div>
            @endif
        </div>
    </flux:card>

    <div class="mt-8 flex gap-3">
        <flux:button size="sm" variant="primary" color="sky" type="submit">
            {{ $submitLabel }}
        </flux:button>
        <flux:button size="sm" as="a" variant="ghost" href="{{ $cancelHref }}">
            {{ __('Cancel') }}
        </flux:button>
    </div>
</form>
