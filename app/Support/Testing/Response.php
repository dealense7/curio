<?php

declare(strict_types=1);

namespace App\Support\Testing;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

use function json_decode;

use PHPUnit\Framework\Assert;

class Response extends TestResponse
{
    protected static array $successResponseStructure = [];

    protected static array $errorResponseStructure = [];

    protected static array $pagerMetaStructure = [
        'total',
        'count',
        'perPage',
        'currentPage',
        'totalPages',
        'links',
    ];

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

    public function assertJsonDataCount(int $count): self
    {
        Assert::assertCount($count, $this->getDecodedContent()['data'] ?? []);

        return $this;
    }

    /** @param array{page: int, perPage: int, count: int, total: int} $data */
    public function assertJsonDataPagination(array $data): self
    {
        $pagination = $this->getDecodedContent()['meta']['pagination'] ?? [];

        Assert::assertSame($data['page'], $pagination['currentPage'] ?? null);
        Assert::assertSame($data['perPage'], $pagination['perPage'] ?? null);
        Assert::assertSame($data['count'], $pagination['count'] ?? null);
        Assert::assertSame($data['total'], $pagination['total'] ?? null);

        return $this;
    }

    public function assertJsonDataCollectionStructure(array $data, bool $includePagerMeta = true): self
    {
        $structure         = self::$successResponseStructure;
        $structure['data'] = [$data];

        if ($includePagerMeta) {
            $structure['meta'] = [
                'pagination' => self::$pagerMetaStructure,
            ];
        }

        $this->assertJsonStructure($structure);

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

    /** @param list<string> $permissions */
    public function assertForbiddenPermissions(array $permissions): self
    {
        Assert::assertTrue(Str::contains((string) $this->json('message'), (string) data_get($permissions, 0)));

        return $this;
    }

    public function assertCreated(): self
    {
        parent::assertCreated();
        $this->assertJsonSuccessStructure();

        return $this;
    }
}
