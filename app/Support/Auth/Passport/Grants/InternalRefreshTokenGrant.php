<?php

declare(strict_types=1);

namespace App\Support\Auth\Passport\Grants;

use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Auth\Passport\Contracts\RefreshTokenBridgeRepositoryContract;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Psr\Http\Message\ServerRequestInterface;

class InternalRefreshTokenGrant extends RefreshTokenGrant
{
    public function __construct(
        RefreshTokenBridgeRepositoryContract $refreshTokenRepository,
        private readonly AuthServiceContract $authService,
    ) {
        parent::__construct($refreshTokenRepository);
    }

    public function getIdentifier(): string
    {
        return 'internal_refresh_token';
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateOldRefreshToken(ServerRequestInterface $request, string $clientId): array
    {
        $refreshToken = parent::validateOldRefreshToken($request, $clientId);
        $userId       = filter_var($refreshToken['user_id'] ?? null, FILTER_VALIDATE_INT);

        if ($userId === false || $this->authService->retrieveUserById($userId) === null) {
            throw OAuthServerException::invalidRefreshToken('User is not eligible to authenticate');
        }

        return $refreshToken;
    }
}
