<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\User\UserContactType;
use App\Models\User\User;

class UserObserver
{
    public function created(User $user): void
    {
        $this->syncPrimaryEmailContact($user);
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['password', 'archived_at', 'email_verified_at', 'status'])) {
            $user->revokeAccessTokens();
        }

        if ($user->wasChanged('email')) {
            $this->syncPrimaryEmailContact($user);
        }
    }

    private function syncPrimaryEmailContact(User $user): void
    {
        $contact = $user->contacts()->firstOrNew([
            'type'       => UserContactType::EMAIL,
            'is_primary' => true,
        ]);

        $contact->forceFill([
            'label'       => null,
            'value'       => $user->getEmail(),
            'is_primary'  => true,
            'archived_at' => null,
        ])->save();
    }
}
