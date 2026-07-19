<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'grant_type'    => $this->trimmedInput('grant_type'),
            'client_id'     => $this->trimmedInput('client_id'),
            'login'         => $this->trimmedInput('login'),
            'refresh_token' => $this->trimmedInput('refresh_token'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'grant_type'    => ['required', 'string', 'max:100'],
            'client_id'     => ['required', 'uuid'],
            'client_secret' => ['sometimes', 'nullable', 'string', 'max:4096'],
            'login'         => ['exclude_unless:grant_type,internal', 'required', 'email:rfc', 'max:255'],
            'password'      => ['exclude_unless:grant_type,internal', 'required', 'string', 'max:4096'],
            'refresh_token' => ['exclude_unless:grant_type,internal_refresh_token', 'required', 'string', 'max:4096'],
            'scope'         => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    private function trimmedInput(string $key): mixed
    {
        $value = $this->input($key);

        return is_string($value) ? trim($value) : $value;
    }
}
