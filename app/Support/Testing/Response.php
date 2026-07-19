<?php

declare(strict_types=1);

namespace App\Support\Testing;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;

use function json_decode;

class Response extends TestResponse
{
    protected static array $successResponseStructure = [];

    protected static array $errorResponseStructure = [];

    public static function setSuccessResponseStructure(array $structure): void
    {
        self::$successResponseStructure = $structure;
    }

    public static function setErrorResponseStructure(array $structure): void
    {
        self::$errorResponseStructure = $structure;
    }

    public function getDecodedContent(): array
    {
        /** @var array $content */
        $content = json_decode((string) $this->getContent(), true);

        return $content;
    }

    public function assertJsonDataItemStructure(array $data): self
    {
        $this->assertJsonStructure(['data' => $data]);

        return $this;
    }

    public function assertJsonErrorStructure(): self
    {
        $this->assertJsonStructure(self::$errorResponseStructure);

        return $this;
    }

    public function assertJsonSuccessStructure(string $message = 'ok'): self
    {
        $this->assertJsonStructure(self::$successResponseStructure);
        $this->assertJson(['message' => $message]);

        return $this;
    }

    public function assertErrorMessage(string $message): self
    {
        $this->assertJsonErrorStructure();
        $this->assertJson(['message' => $message]);

        return $this;
    }

    public function assertAuthenticationFailed(): self
    {
        parent::assertUnauthorized();

        return $this->assertErrorMessage(__('oauth.client-authentication-failed'));
    }

    public function assertGrantTypeFailed(): self
    {
        return $this->assertErrorMessage(__('oauth.the-authorization-grant-type-is-not-supported-by-the-authorization-server'));
    }

    public function assertUnauthorized(): self
    {
        parent::assertUnauthorized();

        return $this;
    }

    public function assertCreated(): self
    {
        parent::assertCreated();
        $this->assertJsonSuccessStructure();

        return $this;
    }
}
