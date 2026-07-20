<?php

namespace App\Enums;

enum Locale: string
{
    case En = 'en';
    case De = 'de';

    public function label(): string
    {
        return match ($this) {
            Locale::En => 'English',
            Locale::De => 'Deutsch',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            Locale::En => 'EN',
            Locale::De => 'DE',
        };
    }
}

