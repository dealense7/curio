<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Retailer;

use Illuminate\Foundation\Http\FormRequest;

class IndexRetailerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filters'                    => ['sometimes', 'array'],
            'filters.name'               => ['sometimes', 'string', 'max:160'],
            'filters.slug'               => ['sometimes', 'string', 'max:180'],
            'filters.domain'             => ['sometimes', 'string', 'max:255'],
            'filters.is_active'          => ['sometimes', 'boolean'],
            'filters.scraping_enabled'   => ['sometimes', 'boolean'],
            'page'                       => ['sometimes', 'integer', 'min:1'],
            'perPage'                    => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort'                       => ['sometimes', 'string', 'max:40'],
        ];
    }
}
