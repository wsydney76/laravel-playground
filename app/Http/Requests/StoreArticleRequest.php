<?php

namespace App\Http\Requests;

use App\Enums\State;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title.*'          => ['required', 'string', 'min:10', 'max:255'],
            'slug.*'           => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'body.*'           => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:4096'],
            'state'          => ['nullable', Rule::enum(State::class)],
        ];
    }
}

