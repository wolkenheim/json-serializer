<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

class PropertyRule
{
    public function __construct(
        public string       $accessName,
        public string       $jsonName,
        public ?string      $fieldFormatClass,
        public PropertyType $propertyType,
    )
    {
    }
}
