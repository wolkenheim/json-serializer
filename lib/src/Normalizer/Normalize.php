<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Normalizer;

interface Normalize
{
    public function normalize(mixed $data): mixed;
}
