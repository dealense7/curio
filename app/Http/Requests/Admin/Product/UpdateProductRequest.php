<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Product;

class UpdateProductRequest extends StoreProductRequest
{
    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'nullable', 'string', 'ulid'],
            'name'        => ['sometimes', 'string', 'max:160'],
            'brand'       => ['nullable', 'string', 'max:120'],
            'gtin'        => ['nullable', 'string', 'regex:/^(?:\d{8}|\d{12}|\d{13}|\d{14})$/'],
            'size_value'  => ['nullable', 'numeric'],
            'size_unit'   => ['nullable', 'string', 'max:30'],
            'pack_count'  => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }
}
