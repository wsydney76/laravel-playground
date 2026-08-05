<flux:navbar.item :current="request()->routeIs('articles.*')" href="{{ route('articles.index', ['locale' => app()->getLocale()]) }}">
    {{ __('Articles') }}
</flux:navbar.item>

<flux:navbar.item :current="request()->routeIs('topics.*')" href="{{ route('topics.index', ['locale' => app()->getLocale()]) }}">
    {{ __('Topics') }}
</flux:navbar.item>
