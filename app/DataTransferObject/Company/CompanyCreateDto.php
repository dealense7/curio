<?php

declare(strict_types=1);

namespace App\DataTransferObject\Company;

use App\Models\User;

class CompanyCreateDto
{
    public function __construct(
        public string $displayName,
        public ?string $legalName,
        public ?string $slug,
        public int $countryId,
        public int $defaultCurrencyId,
        public string $timezone,
        public string $defaultLocale,
        public ?string $supportEmail,
        public ?string $supportPhone,
        public ?string $websiteUrl,
        public ?string $logoPath,
        public ?User $createdBy,
    ) {
        //
    }

    /** @param array<string, mixed> $data */
    public static function fromValidatedData(
        array $data,
        int $countryId,
        int $defaultCurrencyId,
        ?User $createdBy,
    ): self {
        return new self(
            displayName: $data['display_name'],
            legalName: $data['legal_name'] ?? null,
            slug: $data['slug']            ?? null,
            countryId: $countryId,
            defaultCurrencyId: $defaultCurrencyId,
            timezone: $data['timezone'],
            defaultLocale: $data['default_locale'] ?? 'en',
            supportEmail: $data['support_email']   ?? null,
            supportPhone: $data['support_phone']   ?? null,
            websiteUrl: $data['website_url']       ?? null,
            logoPath: $data['logo_path']           ?? null,
            createdBy: $createdBy,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'display_name'        => $this->displayName,
            'legal_name'          => $this->legalName,
            'slug'                => $this->slug,
            'country_id'          => $this->countryId,
            'default_currency_id' => $this->defaultCurrencyId,
            'timezone'            => $this->timezone,
            'default_locale'      => $this->defaultLocale,
            'support_email'       => $this->supportEmail,
            'support_phone'       => $this->supportPhone,
            'website_url'         => $this->websiteUrl,
            'logo_path'           => $this->logoPath,
        ];
    }
}
