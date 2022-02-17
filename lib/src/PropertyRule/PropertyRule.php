<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

class PropertyRule
{
    public function __construct(
        public string $name,
        public ?string $jsonName,
        public ?string $fieldFormatClass,
    )
    {
    }
}
