<?php

declare(strict_types=1);

namespace App\CacheRepositories;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

abstract class CacheRepository
{
    private const string NULL_SENTINEL = '__cache_null__';

    protected string $cacheKey = '';

    protected int $cacheTtl = 1800;

    private ?bool $supportsTags = null;

    private ?string $namespace = null;

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    protected function remember(string $key, Closure $callback): mixed
    {
        if ($this->supportsTags()) {
            return Cache::tags([$this->cacheKey])->remember($key, $this->cacheTtl, $callback);
        }

        return Cache::remember($key, $this->cacheTtl, $callback);
    }

    protected function rememberNullable(string $key, Closure $callback): mixed
    {
        if ($this->supportsTags()) {
            $value = Cache::tags([$this->cacheKey])->remember($key, $this->cacheTtl, function () use ($callback): mixed {
                return $callback() ?? self::NULL_SENTINEL;
            });

            return $value === self::NULL_SENTINEL ? null : $value;
        }

        $value = Cache::remember($key, $this->cacheTtl, function () use ($callback): mixed {
            return $callback() ?? self::NULL_SENTINEL;
        });

        return $value === self::NULL_SENTINEL ? null : $value;
    }

    protected function flushTag(): void
    {
        if ($this->supportsTags()) {
            Cache::tags([$this->cacheKey])->flush();

            return;
        }

        Cache::forever($this->getNamespaceKey(), Str::uuid()->toString());
    }

    /**
     * @param  array<int, mixed>  $args
     */
    protected function createKey(string $prefix, array $args = []): string
    {
        return $this->cacheKey.':'.$this->getNamespace().':'.$prefix.':'.sha1(serialize($args));
    }

    protected function clear(): void
    {
        $this->flushTag();
    }

    private function getNamespace(): string
    {
        if ($this->namespace !== null) {
            return $this->namespace;
        }

        /** @var string|null $namespace */
        $namespace = Cache::get($this->getNamespaceKey());

        if ($namespace === null) {
            $namespace = 'v1';
            Cache::forever($this->getNamespaceKey(), $namespace);
        }

        return $this->namespace = $namespace;
    }

    private function getNamespaceKey(): string
    {
        return $this->cacheKey.':namespace';
    }

    private function supportsTags(): bool
    {
        if ($this->supportsTags !== null) {
            return $this->supportsTags;
        }

        return $this->supportsTags = Cache::getStore() instanceof TaggableStore;
    }
}
