<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
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
            'filters.brand'       => ['sometimes', 'string', 'max:120'],
            'filters.gtin'        => ['sometimes', 'string', 'max:14'],
            'filters.category_id' => ['sometimes', 'nullable', 'string', 'ulid'],
            'filters.is_active'   => ['sometimes', 'boolean'],
            'page'                => ['sometimes', 'integer', 'min:1'],
            'perPage'             => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort'                => ['sometimes', 'string', 'max:80'],
        ];
    }
}
