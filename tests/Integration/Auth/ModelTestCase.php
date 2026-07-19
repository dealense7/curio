<?php

declare(strict_types=1);

namespace Tests\Integration\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Integration\IntegrationTestCase;

class ModelTestCase extends IntegrationTestCase
{
    use DatabaseTransactions;

    protected static function getModel(): User
    {
        return new User;
    }
}
