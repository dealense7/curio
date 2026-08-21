<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Acl\DefaultRoles;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy extends Policy
{
    public function read(User $user, ?Company $company = null): Response
    {
        if ($user->can(Company::getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Company::getPermission('read'));
    }

    public function create(User $user): Response
    {
        if ($user->can(Company::getPermission('create'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Company::getPermission('create'));
    }

    public function manageSettings(User $user, Company $company): Response
    {
        if ($user->hasRole(DefaultRoles::ADMINISTRATOR->value)) {
            return $this->allow();
        }

        if ((int) $user->getAttribute('company_id') !== $company->getId()) {
            return $this->denyWithCustomMessage('You cannot access settings for another company.');
        }

        if ($user->can(Company::getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Company::getPermission('read'));
    }

    public function suspend(User $user, Company $company): Response
    {
        if ($user->can(Company::getPermission('suspend'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Company::getPermission('suspend'));
    }

    public function reactivate(User $user, Company $company): Response
    {
        if ($user->can(Company::getPermission('reactivate'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Company::getPermission('reactivate'));
    }

    public function archive(User $user, Company $company): Response
    {
        if ($user->can(Company::getPermission('archive'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Company::getPermission('archive'));
    }
}
