<flux:navbar.item :current="request()->routeIs('articles.*')" href="{{ route('articles.index', ['locale' => app()->getLocale()]) }}">
    {{ __('Articles') }}
</flux:navbar.item>



