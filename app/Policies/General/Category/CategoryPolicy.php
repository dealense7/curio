<?php

declare(strict_types=1);

namespace App\Policies\General\Category;

use App\Models\General\Category\Category;
use App\Models\User\User;
use App\Policies\Policy;
use Illuminate\Auth\Access\Response;

class CategoryPolicy extends Policy
{
    public function read(User $user, ?Category $item = null): Response
    {
        if ($user->can(Category::getPermission('read'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Category::getPermission('read'));
    }

    public function create(User $user): Response
    {
        if ($user->can(Category::getPermission('create'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Category::getPermission('create'));
    }

    public function update(User $user, Category $item): Response
    {
        if ($user->can(Category::getPermission('update'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Category::getPermission('update'));
    }

    public function delete(User $user, Category $item): Response
    {
        if ($user->can(Category::getPermission('delete'))) {
            return $this->allow();
        }

        return $this->denyWithMessage(Category::getPermission('delete'));
    }
}
