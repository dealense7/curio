<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Retailer\Retailer;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class RetailerPolicy extends Policy
{
    public function read(User $user, Retailer $item): Response
    {
        if ($user->can($item->getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('read'));
    }

    public function create(User $user, Retailer $item): Response
    {
        if ($user->can($item->getPermission('create'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('create'));
    }

    public function update(User $user, Retailer $item): Response
    {
        if ($user->can($item->getPermission('update'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('update'));
    }

    public function delete(User $user, Retailer $item): Response
    {
        if ($user->can($item->getPermission('delete'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('delete'));
    }
}
