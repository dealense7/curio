<?php

declare(strict_types=1);

use App\Enums\UserContactType;
use App\Enums\UserStatus;
use App\Models\User;
use App\Models\UserContact;
use Illuminate\Database\QueryException;
use Tests\Integration\Auth\ModelTestCase;

uses(ModelTestCase::class);

it('should normalize email and synchronize the primary email contact', function (): void {
    $user = User::factory()->create([
        'email' => '  USER@Example.COM  ',
    ]);

    expect($user->getEmail())->toBe('user@example.com');

    $this->assertDatabaseHas('user_contacts', [
        'user_id'    => $user->getId(),
        'type'       => UserContactType::EMAIL->value,
        'value'      => 'user@example.com',
        'is_primary' => true,
    ]);

    $user->update(['email' => ' New@Example.COM ']);

    $this->assertDatabaseHas('user_contacts', [
        'user_id'    => $user->getId(),
        'type'       => UserContactType::EMAIL->value,
        'value'      => 'new@example.com',
        'is_primary' => true,
    ]);
});

it('should reject duplicate normalized authentication emails', function (): void {
    User::factory()->create(['email' => 'user@example.com']);

    expect(fn () => User::factory()->create(['email' => ' USER@EXAMPLE.COM ']))
        ->toThrow(QueryException::class);
});

it('should provide a full name', function (): void {
    $user = User::factory()->create([
        'first_name' => 'Ada',
        'last_name'  => 'Lovelace',
    ]);

    expect($user->getFullName())->toBe('Ada Lovelace');
});

it('should cast user status and provide factory states', function (): void {
    expect(User::factory()->active()->make()->status)->toBe(UserStatus::ACTIVE)
        ->and(User::factory()->invited()->make()->status)->toBe(UserStatus::INVITED)
        ->and(User::factory()->suspended()->make()->status)->toBe(UserStatus::SUSPENDED);
});

it('should enforce one primary contact', function (): void {
    $user = User::factory()->create();

    $firstPhone = UserContact::factory()->for($user)->primary()->create([
        'value' => ' +995 555 123 456 ',
    ]);
    $secondPhone = UserContact::factory()->for($user)->create();

    expect($firstPhone->fresh()->getValue())->toBe('+995 555 123 456')
        ->and($secondPhone->fresh()->isPrimary())->toBeFalse();
});

it('should hide password and remember token from serialization', function (): void {
    $serialized = User::factory()->make()->toArray();

    expect(array_key_exists('password', $serialized))->toBeFalse()
        ->and(array_key_exists('remember_token', $serialized))->toBeFalse();
});
