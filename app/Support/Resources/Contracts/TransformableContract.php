<?php

declare(strict_types=1);

namespace App\Support\Resources\Contracts;

interface TransformableContract
{
    public function getKey();

    public function getHidden();
}
