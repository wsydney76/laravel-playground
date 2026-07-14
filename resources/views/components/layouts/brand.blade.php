<flux:brand
    :href="route('home')"
    logo="https://fluxui.dev/img/demo/logo.png"
    name="Acme Inc."
    {{ $attributes->class(['w-28 dark:hidden']) }}
/>
<flux:brand
    href="#"
    logo="https://fluxui.dev/img/demo/dark-mode-logo.png"
    name="Acme Inc."
    class="hidden w-28 dark:flex"
/>
