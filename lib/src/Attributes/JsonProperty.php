<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
final class JsonProperty
{
    public function __construct(public string $serializedName)
    {
    }
}
