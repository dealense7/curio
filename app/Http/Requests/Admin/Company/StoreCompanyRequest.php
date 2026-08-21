<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $payload = [
            'display_name'  => is_string($this->input('display_name')) ? trim($this->string('display_name')->toString()) : $this->input('display_name'),
            'legal_name'    => $this->nullableString('legal_name'),
            'slug'          => $this->nullableString('slug'),
            'support_email' => $this->nullableString('support_email'),
            'website_url'   => $this->nullableString('website_url'),
            'logo_path'     => $this->nullableString('logo_path'),
        ];

        if (is_string($this->input('support_phone'))) {
            $payload['support_phone'] = preg_replace('/[^+0-9]/', '', $this->string('support_phone')->toString());
        }

        $this->merge($payload);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'display_name'        => ['required', 'string', 'max:120'],
            'legal_name'          => ['nullable', 'string', 'max:180'],
            'slug'                => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'country_id'          => ['required', 'string', 'ulid'],
            'default_currency_id' => ['required', 'string', 'ulid'],
            'timezone'            => ['required', 'string', 'max:64', Rule::in(timezone_identifiers_list())],
            'default_locale'      => ['sometimes', 'string', 'max:12'],
            'support_email'       => ['nullable', 'email:rfc', 'max:254'],
            'support_phone'       => ['nullable', 'string', 'max:32', 'regex:/^\+[1-9][0-9]{6,14}$/'],
            'website_url'         => ['nullable', 'url:http,https', 'max:2048'],
            'logo_path'           => ['nullable', 'string', 'max:500'],
        ];
    }

    private function nullableString(string $key): ?string
    {
        if (! is_string($this->input($key))) {
            return $this->input($key);
        }

        $value = trim($this->string($key)->toString());

        return $value === '' ? null : $value;
    }
}
