<?php

declare(strict_types=1);

namespace Tests\Integration\Admin\Retailer;

use App\Models\Retailer\Retailer;
use App\Support\Testing\ProvidesTestingData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): Retailer
    {
        return new Retailer;
    }

    /**
     * @param  array<int, string>  $abilities
     * @return array<int, string>
     */
    protected function getPermissions(array $abilities): array
    {
        return array_map(
            static fn (string $ability): string => Retailer::getPermission($ability),
            $abilities,
        );
    }

    /** @return array<string, mixed> */
    protected function getRetailerData(): array
    {
        $country  = ProvidesTestingData::createCountryRandomItem()->firstOrFail();
        $currency = ProvidesTestingData::createCurrencyRandomItem()->firstOrFail();

        return [
            'name'              => 'Example Retailer',
            'slug'              => 'example-retailer',
            'domain'            => 'example.com',
            'country_id'        => $country->getPublicId(),
            'currency_id'       => $currency->getPublicId(),
            'is_active'         => true,
            'scraping_enabled'  => true,
        ];
    }
}
