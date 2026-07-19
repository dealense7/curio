<?php

declare(strict_types=1);

namespace App\Support\Resources;

use App\Support\Resources\Contracts\TransformableContract;
use Illuminate\Support\Str;
use LogicException;

use function call_user_func_array;
use function in_array;
use function is_array;
use function key;
use function method_exists;

trait TransformsData
{
    protected static array $transformMapping = [];

    protected static array $hideInOutput = [];

    /**
     * @var array<class-string, array<string, array{0: string, 1: string, 2: bool}>>
     */
    private static array $parsedTransformMappingCache = [];

    public static function getTransformFields(): array
    {
        return static::$transformMapping;
    }

    public static function getHideInOutput(): array
    {
        return static::$hideInOutput;
    }

    public static function transformToApi(TransformableContract $model): array
    {
        $fields = static::getParsedTransformFields();
        $hiddenProperties = array_fill_keys($model->getHidden(), true);
        $hideInOutput = array_fill_keys(static::getHideInOutput(), true);
        $transformed = [];

        foreach ($fields as $internalField => [$key, $method, $methodOnResource]) {
            if (isset($hiddenProperties[$internalField])) {
                continue;
            }

            if (isset($hideInOutput[$internalField])) {
                continue;
            }

            if ($methodOnResource) {
                $value = call_user_func_array([static::class, $method], ['model' => $model]);
            } else {
                $value = $model->$method();
            }

            $transformed[$key] = $value;
        }

        return $transformed;
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: bool}>
     */
    private static function getParsedTransformFields(): array
    {
        if (isset(self::$parsedTransformMappingCache[static::class])) {
            return self::$parsedTransformMappingCache[static::class];
        }

        $parsedFields = [];

        foreach (static::getTransformFields() as $internalField => $transformValue) {
            if (is_array($transformValue)) {
                $key = (string) key($transformValue);
                $method = (string) $transformValue[$key];

                $parsedFields[$internalField] = [$key, $method, method_exists(static::class, $method)];

                continue;
            }

            $key = (string) $transformValue;
            $parsedFields[$internalField] = [
                $key,
                'get'.ucwords(Str::camel($key)),
                false,
            ];
        }

        return self::$parsedTransformMappingCache[static::class] = $parsedFields;
    }

    public static function transformToInternal(array $fields): array
    {
        $modelTransformedFields = [];

        foreach (static::getTransformFields() as $key => $transformField) {
            if (is_array($transformField)) {
                $modelTransformedFields[key($transformField)] = $key;
            } else {
                $modelTransformedFields[$transformField] = $key;
            }
        }

        $transformed = [];

        foreach ($fields as $fieldKey => $postValue) {
            if (isset($modelTransformedFields[$fieldKey])) {
                $transformed[$modelTransformedFields[$fieldKey]] = $postValue;
            }
        }

        return $transformed;
    }

    private static function parseKeyValue(string $internalField, mixed $transformValue, TransformableContract $model): array
    {
        if (is_array($transformValue)) {
            $key = key($transformValue);
            $method = $transformValue[$key];

            if (method_exists(static::class, $method)) {
                $value = call_user_func_array([static::class, $method], ['model' => $model]);
            } elseif (method_exists($model, $method)) {
                $value = $model->$method();
            } else {
                throw new LogicException('Method '.$method.' does not available not for resource '.static::class.', not for model '.$model::class);
            }
        } else {
            $method = 'get'.ucwords(Str::camel($transformValue));

            if (method_exists($model, $method)) {
                $key = $transformValue;
                $value = $model->$method();
            } else {
                $method = 'get'.ucwords(Str::camel($internalField));

                if (! method_exists($model, $method)) {
                    throw new LogicException('Field '.$internalField.' getter ('.$method.') does not available for model '.$model::class);
                }

                $key = $transformValue;
                $value = $model->$method();
            }
        }

        return [$key, $value];
    }
}
