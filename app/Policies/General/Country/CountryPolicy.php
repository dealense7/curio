<?php

declare(strict_types=1);

namespace App\Policies\General\Country;

use App\Models\General\Country\Country;
use App\Models\User;
use App\Policies\Policy;
use Illuminate\Auth\Access\Response;

class CountryPolicy extends Policy
{
    public function read(User $user, ?Country $item = null): Response
    {
        if ($user->can(Country::getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Country::getPermission('read'));
    }

    public function create(User $user): Response
    {
        if ($user->can(Country::getPermission('create'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Country::getPermission('create'));
    }

    public function update(User $user, Country $item): Response
    {
        if ($user->can(Country::getPermission('update'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Country::getPermission('update'));
    }

    public function delete(User $user, Country $item): Response
    {
        if ($user->can(Country::getPermission('delete'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Country::getPermission('delete'));
    }
}
