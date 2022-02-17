<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer;

interface Serialize
{
    public function serialize(mixed $data): string;
}
