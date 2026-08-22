<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filters'             => ['sometimes', 'array'],
            'filters.name'        => ['sometimes', 'string', 'max:160'],
            'filters.slug'        => ['sometimes', 'string', 'max:180'],
            'filters.parent_id'   => ['sometimes', 'nullable', 'string', 'ulid'],
            'page'                => ['sometimes', 'integer', 'min:1'],
            'perPage'             => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort'                => ['sometimes', 'string', 'max:40'],
        ];
    }
}
