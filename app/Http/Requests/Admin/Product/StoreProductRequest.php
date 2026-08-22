<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'string', 'ulid'],
            'name'        => ['required', 'string', 'max:160'],
            'brand'       => ['nullable', 'string', 'max:120'],
            'gtin'        => ['nullable', 'string', 'regex:/^(?:\d{8}|\d{12}|\d{13}|\d{14})$/'],
            'size_value'  => ['nullable', 'numeric'],
            'size_unit'   => ['nullable', 'string', 'max:30'],
            'pack_count'  => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'      => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'brand'     => is_string($this->input('brand')) ? trim($this->input('brand')) : $this->input('brand'),
            'gtin'      => is_string($this->input('gtin')) ? trim($this->input('gtin')) : $this->input('gtin'),
            'size_unit' => is_string($this->input('size_unit')) ? strtolower(trim($this->input('size_unit'))) : $this->input('size_unit'),
        ]);
    }
}
