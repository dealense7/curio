<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\TokenRequest;
use App\Http\Resources\Auth\AuthTokenResource;
use App\Http\Resources\Auth\CurrentUserResource;
use App\Models\User\User;
use App\Support\Auth\Passport\Contracts\AuthServiceContract;
use App\Support\Resources\Responses\ApiResponse;
use App\Support\Resources\Responses\ArrayResource;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as LaravelResponse;
use Laravel\Passport\Http\Controllers\HandlesOAuthErrors;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthController extends ApiController
{
    use HandlesOAuthErrors;

    public function __construct(private readonly AuthorizationServer $server) {}

    public function token(TokenRequest $validatedRequest, ServerRequestInterface $request): JsonResponse
    {
        // Resolving the form request performs validation before Passport handles
        // the PSR-7 request. Passport still owns the OAuth response semantics.
        $validatedRequest->validated();

        try {
            $response = $this->convertResponse(
                $this->server->respondToAccessTokenRequest($request, new Response),
            );
        } catch (OAuthServerException $exception) {
            if ($exception->getErrorType() === 'invalid_grant') {
                $exception = new OAuthServerException(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getErrorType(),
                    LaravelResponse::HTTP_UNAUTHORIZED,
                );
            } elseif (! in_array($exception->getErrorType(), [
                'invalid_client',
                'invalid_request',
                'invalid_refresh_token',
                'unauthorized_client',
                'unsupported_grant_type',
            ], true)) {
                report($exception);
            }

            return $this->error(
                $this->getOauthErrorMessage($exception),
                $exception->getHttpStatusCode(),
            );
        }

        $stream = $response->getBody();
        $stream->rewind();
        /** @var array<string, mixed> $data */
        $data = json_decode($stream->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return ApiResponse::resource(__('api.ok'), $data, AuthTokenResource::class)
            ->response()
            ->setStatusCode(SymfonyResponse::HTTP_CREATED);
    }

    public function currentUser(AuthServiceContract $authService): JsonResponse
    {
        $user = $authService->getUser(['contacts']);
        if ($user === null) {
            return $this->resourceNotFound();
        }

        return $this->resource($user, CurrentUserResource::class, ['contacts']);
    }

    public function permissions(AuthServiceContract $authService): JsonResponse
    {
        $user = $authService->getUser();
        if ($user === null) {
            return $this->resourceNotFound();
        }

        /** @var User $user */
        $data = [
            'roles'       => $user->getRoleNames()->values()->all(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
        ];

        return $this->resource($data, ArrayResource::class);
    }

    public function revokeToken(AuthServiceContract $authService): JsonResponse
    {
        $authService->revokeToken();

        return $this->success(__('oauth.token_revoked'));
    }

    protected function convertResponse(ResponseInterface $psrResponse): Response
    {
        return new Response(
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders(),
            $psrResponse->getBody(),
        );
    }

    protected function getOauthErrorMessage(OAuthServerException $exception): string
    {
        return match ($exception->getErrorType()) {
            'invalid_client'          => __('oauth.client-authentication-failed'),
            'invalid_grant'           => __('oauth.the-user-credentials-were-incorrect'),
            'invalid_request'         => __('oauth.invalid_request'),
            'invalid_refresh_token'   => __('oauth.invalid_refresh_token'),
            'unsupported_grant_type'  => __('oauth.the-authorization-grant-type-is-not-supported-by-the-authorization-server'),
            'unauthorized_client'     => __('oauth.unauthorized_client'),
            default                   => __('oauth.server_error'),
        };
    }
}
