<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Repositories;

use App\Support\Auth\Passport\Contracts\ClientRepositoryContract;
use Laravel\Passport\ClientRepository as BaseClientRepository;

class ClientRepository extends BaseClientRepository implements ClientRepositoryContract
{
    //
}
