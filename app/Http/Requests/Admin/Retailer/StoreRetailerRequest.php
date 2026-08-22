<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Retailer;

use Illuminate\Foundation\Http\FormRequest;

class StoreRetailerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:160'],
            'slug'              => ['required', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'domain'            => ['nullable', 'string', 'max:255'],
            'country_id'        => ['required', 'string', 'ulid'],
            'currency_id'       => ['required', 'string', 'ulid'],
            'is_active'         => ['sometimes', 'boolean'],
            'scraping_enabled'  => ['sometimes', 'boolean'],
            'last_scraped_at'   => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'   => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'slug'   => is_string($this->input('slug')) ? strtolower(trim($this->input('slug'))) : $this->input('slug'),
            'domain' => is_string($this->input('domain')) ? strtolower(trim($this->input('domain'))) : $this->input('domain'),
        ]);
    }
}
