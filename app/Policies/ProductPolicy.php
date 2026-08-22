<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product\Product;
use App\Models\User\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy extends Policy
{
    public function read(User $user, Product $item): Response
    {
        if ($user->can($item->getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('read'));
    }

    public function create(User $user, Product $item): Response
    {
        if ($user->can($item->getPermission('create'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('create'));
    }

    public function update(User $user, Product $item): Response
    {
        if ($user->can($item->getPermission('update'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('update'));
    }

    public function delete(User $user, Product $item): Response
    {
        if ($user->can($item->getPermission('delete'))) {
            return $this->allow();
        }

        return $this->denyWithMessage($item->getPermission('delete'));
    }
}
