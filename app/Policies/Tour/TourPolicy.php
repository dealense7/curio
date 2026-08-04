<?php

declare(strict_types=1);

namespace App\Policies\Tour;

use App\Models\Tour\Tour;
use App\Models\User;
use App\Policies\Policy;
use Illuminate\Auth\Access\Response;

class TourPolicy extends Policy
{
    public function read(User $user, ?Tour $tour = null): Response
    {
        return $user->can(Tour::getPermission('read'))
            ? $this->allow()
            : $this->denyWithMessage(Tour::getPermission('read'));
    }

    public function create(User $user): Response
    {
        return $user->can(Tour::getPermission('create'))
            ? $this->allow()
            : $this->denyWithMessage(Tour::getPermission('create'));
    }

    public function update(User $user, Tour $tour): Response
    {
        return $user->can(Tour::getPermission('update'))
            ? $this->allow()
            : $this->denyWithMessage(Tour::getPermission('update'));
    }

    public function delete(User $user, Tour $tour): Response
    {
        return $user->can(Tour::getPermission('delete'))
            ? $this->allow()
            : $this->denyWithMessage(Tour::getPermission('delete'));
    }
}
