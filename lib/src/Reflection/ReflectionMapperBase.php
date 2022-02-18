<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Reflection;

use ReflectionNamedType;
use Wolkenheim\JsonSerializer\FieldFormat\DateTimeFormat;
use Wolkenheim\JsonSerializer\FieldFormat\EnumFormat;


abstract class ReflectionMapperBase
{
    protected array $defaultStrategyClassMappings = [
        'DateTime' => DateTimeFormat::class,
        'Enum' => EnumFormat::class
    ];

    /**
     * Default Value Format Rule for non scalar fields
     * see types: https://www.php.net/manual/en/language.types.intro.php
     */
    public function getDefaultValueFormatClass(?\ReflectionType $propertyType): ?string
    {
        // union types
        // else type is ReflectionNamedType and has one specific type and could be nullable
        if (
            is_null($propertyType)
            || get_class($propertyType) == \ReflectionUnionType::class
            || get_class($propertyType) == \ReflectionIntersectionType::class
            || $propertyType->getName() === 'mixed'
        ) {
            return null;
        }

        /** @var ReflectionNamedType $reflectionNamedType */
        $reflectionNamedType = $propertyType;
        $typeName = $reflectionNamedType->getName();

        if ($typeName === 'array') {
            return null;
        }

        // objects
        // callables
        // iterables

        return $this->getDefaultStrategyForType($typeName);
    }

    public function getDefaultStrategyForType(string $typeName): ?string
    {
        // is scalar type name
        if (in_array($typeName, ['string', 'int', 'bool', 'float'])) {
            return null;
        }

        if (enum_exists($typeName)) {
            $typeName = 'Enum';
        }

        $typeNames = array_keys($this->defaultStrategyClassMappings);
        if (in_array($typeName, $typeNames)) {
            return $this->defaultStrategyClassMappings[$typeName];
        }

        return null;
    }

    public static function reflectTypeFromValue(mixed $propertyValue): ?string
    {
        return match (gettype($propertyValue)) {
            'boolean' => 'bool',
            'string' => 'string',
            'integer' => 'int',
            'double' => 'float',
            'array' => 'array',
            'object' => 'object',
            default => null,
        };
    }
}
