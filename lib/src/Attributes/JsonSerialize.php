<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class JsonSerialize
{
    public function __construct(public string $className)
    {
    }
}
