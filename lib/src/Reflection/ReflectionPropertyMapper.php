<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Reflection;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;
use Wolkenheim\JsonSerializer\Attributes\JsonSerialize;
use Wolkenheim\JsonSerializer\Exception\InvalidFormatClassException;
use Wolkenheim\JsonSerializer\FieldFormat\Format;
use Wolkenheim\JsonSerializer\PropertyRule\PropertyRuleMapper;


class ReflectionPropertyMapper extends ReflectionMapperBase
{
    public function getName(\ReflectionProperty $property): string
    {
        return $property->getName();
    }

    public function getJsonName(\ReflectionProperty $property): string
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === JsonProperty::class) {
                return $attribute->getArguments()[0];
            }
        }
        return $property->getName();
    }

    public function isIgnored(\ReflectionProperty $property): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === JsonIgnore::class) {
                return true;
            }
        }

        if ($property->isPrivate() || $property->isProtected()) {
            return true;
        }

        return false;
    }

    /**
     * Get processing class for field value
     * @throws InvalidFormatClassException
     */
    public function getFieldFormatClass(\ReflectionProperty $property): ?string
    {
        if (!is_null($attributeFormatClass = $this->getAttributeFormatClass($property))) {
            return $attributeFormatClass;
        }
        return $this->getDefaultValueFormatClass($property->getType());
    }


    // @todo: can this be refactored to getDefaultValueFormatClass and return type lambda or callable?
    public function getChildrenRules(mixed $propertyValue, \ReflectionProperty $property): array
    {
        if (get_class($property->getType()) !== \ReflectionNamedType::class) {
            return [];
        }

        $typeName = $property->getType()->getName();

        if (in_array($typeName, ['string', 'int', 'bool', 'float'])) {
            return [];
        }

        // if is object of class
        if (class_exists($typeName) && !enum_exists($typeName)) {
             return (new PropertyRuleMapper($propertyValue))->getRules();
        }

        return [];
    }


    /**
     * Custom Value Format Rules when PHP Attributes are used
     * @throws InvalidFormatClassException
     */
    public function getAttributeFormatClass(\ReflectionProperty $property): ?string
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === JsonSerialize::class) {
                $formatterClass = $attribute->getArguments()[0];

                $interfaces = class_implements($formatterClass);
                if (is_array($interfaces) && in_array(Format::class, $interfaces)) {
                    return $formatterClass;
                }
                throw new InvalidFormatClassException();
            }
        }
        return null;
    }
}
