<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Country;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedCountry(): array
    {
        return $this->safe()->only([
            'iso2',
            'iso3',
            'numeric_code',
            'name',
            'official_name',
            'is_active',
            'sort_order',
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function validatedPhoneCodes(): ?array
    {
        if (! $this->hasPhoneCodes()) {
            return null;
        }

        /** @var array<int, array<string, mixed>> $phoneCodes */
        $phoneCodes = $this->validated('phone_codes', []);

        return $phoneCodes;
    }

    public function hasPhoneCodes(): bool
    {
        return $this->exists('phone_codes');
    }

    protected function prepareForValidation(): void
    {
        $payload = [
            'iso2' => is_string($this->input('iso2')) ? strtoupper(trim($this->string('iso2')->toString())) : $this->input('iso2'),
            'iso3' => is_string($this->input('iso3')) ? strtoupper(trim($this->string('iso3')->toString())) : $this->input('iso3'),
            'numeric_code' => $this->normalizeNullableString('numeric_code'),
            'official_name' => $this->normalizeNullableString('official_name'),
        ];

        if ($this->exists('phone_codes')) {
            $payload['phone_codes'] = collect($this->input('phone_codes', []))
                ->map(static function (mixed $phoneCode): mixed {
                    if (! is_array($phoneCode)) {
                        return $phoneCode;
                    }

                    if (isset($phoneCode['phone_code']) && is_string($phoneCode['phone_code'])) {
                        $phoneCode['phone_code'] = trim($phoneCode['phone_code']);
                    }

                    return $phoneCode;
                })
                ->all();
        }

        $this->merge($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'iso2' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/', Rule::unique('countries', 'iso2')],
            'iso3' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/', Rule::unique('countries', 'iso3')],
            'numeric_code' => ['nullable', 'string', 'size:3', 'regex:/^[0-9]{3}$/', Rule::unique('countries', 'numeric_code')],
            'name' => ['required', 'string', 'max:120'],
            'official_name' => ['nullable', 'string', 'max:180'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'phone_codes' => ['sometimes', 'array'],
            'phone_codes.*.public_id' => ['sometimes', 'string', 'max:26'],
            'phone_codes.*.phone_code' => ['required', 'string', 'max:8', 'regex:/^\+[0-9]+$/'],
            'phone_codes.*.is_primary' => ['sometimes', 'boolean'],
            'phone_codes.*.sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $phoneCodes = $this->validatedPhoneCodes();

            if ($phoneCodes === null) {
                return;
            }

            $primaryCount = collect($phoneCodes)
                ->filter(static fn (array $phoneCode): bool => (bool) ($phoneCode['is_primary'] ?? false))
                ->count();

            if ($primaryCount > 1) {
                $validator->errors()->add('phone_codes', 'Only one phone code may be marked as primary.');
            }

            $duplicates = collect($phoneCodes)
                ->map(static fn (array $phoneCode): string => (string) $phoneCode['phone_code'])
                ->duplicates();

            if ($duplicates->isNotEmpty()) {
                $validator->errors()->add('phone_codes', 'Phone codes must be unique within the same country.');
            }
        });
    }

    private function normalizeNullableString(string $key): ?string
    {
        if (! is_string($this->input($key))) {
            return $this->input($key);
        }

        $value = trim($this->string($key)->toString());

        return $value === '' ? null : $value;
    }
}
