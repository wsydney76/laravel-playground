# Laravel Playground

This is a Laravel Playground project that allows you to experiment with Laravel features and functionalities in a safe environment. 

You can use this project to test out new ideas, learn Laravel, prototype applications, or get familiar developing with AI support.

* Uses Livewire, Flux, Fortify, Spatie Media Library, Vite, Tailwind.
* Authentication and account management are from Laravel's Livewire starter kit.
* Uses an 'Article' model to play around with basic CRUD operations, relationships, and other Laravel features like policies, validation, and notifications.
* Implements a Livewire powered dashboard to administrate articles, users and notifications.
* Includes an (unconfigured) Filament admin panel.

## Versions

### Laravel Playground: 1.0.0

The result of an (informal) workshop.

### Laravel Playground: 2.0.0

Workshop step 2:

Added content localization (en/de) powered by Spatie's Laravel Translatable package.

### Laravel Custom

Maybe all of this escalated a bit, so not very useful for beginners anymore.

So we created a stripped down version of the project, which is more suitable for beginners at [Laravel Custom](https://github.com/wsydney76/laravel-custom)


## Installation

Git clone this repository and run the following commands under DDEV:

```bash
bash setup/install <project_name>
```

Creates an initial admin user with the following credentials:
* Email: `admin@example.com`
* Password: `kirby-tutorial`

Or set up your development environment manually, and manually run the commands from `setup/install`:    

```bash
composer install &&
composer run setup &&
artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
artisan migrate:fresh --seed &&
```

Adjust the `.env` file to your needs.

## Thanks

Thanks Aylin, Lucy, Lori for this amazing workshop.
