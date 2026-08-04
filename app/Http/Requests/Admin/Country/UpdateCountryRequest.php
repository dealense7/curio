<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Country;

use Illuminate\Validation\Rule;

class UpdateCountryRequest extends StoreCountryRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var string $countryPublicId */
        $countryPublicId = (string) $this->route('countryPublicId');

        return [
            'iso2'                     => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/', Rule::unique('countries', 'iso2')->ignore($countryPublicId, 'public_id')],
            'iso3'                     => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/', Rule::unique('countries', 'iso3')->ignore($countryPublicId, 'public_id')],
            'numeric_code'             => ['nullable', 'string', 'size:3', 'regex:/^[0-9]{3}$/', Rule::unique('countries', 'numeric_code')->ignore($countryPublicId, 'public_id')],
            'name'                     => ['required', 'string', 'max:120'],
            'official_name'            => ['nullable', 'string', 'max:180'],
            'is_active'                => ['sometimes', 'boolean'],
            'sort_order'               => ['sometimes', 'integer', 'min:0'],
            'phone_codes'              => ['sometimes', 'array'],
            'phone_codes.*.public_id'  => ['sometimes', 'string', 'max:26'],
            'phone_codes.*.phone_code' => ['required', 'string', 'max:8', 'regex:/^\+[0-9]+$/'],
            'phone_codes.*.is_primary' => ['sometimes', 'boolean'],
            'phone_codes.*.sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
