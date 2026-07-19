<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends StoreArticleRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('article'));
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'slug.*' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'delete_featured_image' => ['nullable', 'boolean'],
        ]);
    }
}
