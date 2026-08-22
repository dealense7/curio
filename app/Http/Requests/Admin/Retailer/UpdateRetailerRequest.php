<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Retailer;

class UpdateRetailerRequest extends StoreRetailerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'              => ['sometimes', 'string', 'max:160'],
            'slug'              => ['sometimes', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'domain'            => ['nullable', 'string', 'max:255'],
            'country_id'        => ['sometimes', 'string', 'ulid'],
            'currency_id'       => ['sometimes', 'string', 'ulid'],
            'is_active'         => ['sometimes', 'boolean'],
            'scraping_enabled'  => ['sometimes', 'boolean'],
            'last_scraped_at'   => ['nullable', 'date'],
        ];
    }
}
