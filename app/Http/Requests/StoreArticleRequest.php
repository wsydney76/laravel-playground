<?php

namespace App\Http\Requests;

use App\Enums\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\Locale;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title.*' => ['required', 'string', 'min:10', 'max:255'],
            'slug.*' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'body.*' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:10000'],
            'state' => ['nullable', Rule::enum(State::class)],
        ];
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (Locale::cases() as $locale) {
            $attributes["title.{$locale->value}"] = __('Title') . " ({$locale->label()})";
            $attributes["slug.{$locale->value}"] = __('Slug') . " ({$locale->label()})";
            $attributes["body.{$locale->value}"] = __('Body') . " ({$locale->label()})";
        }

        return $attributes;
    }
}
