<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;
use Wolkenheim\JsonSerializer\Attributes\JsonSerialize;
use Wolkenheim\JsonSerializer\Exception\InvalidFormatClassException;
use Wolkenheim\JsonSerializer\FieldFormat\Format;

class PropertyRuleMapper
{
    public function __construct(
        protected object $data,
    )
    {
    }

    public function getRules(): array
    {
        return $this->extractRules(new \ReflectionClass($this->data));
    }

    /**
     * @return PropertyRule[]
     */
    protected function extractRules(\ReflectionClass $class): array
    {
        $metadataProperties = [];
        foreach ($class->getProperties() as $property) {
            if ($this->isIgnored($property)) {
                continue;
            }
            $metadataProperties[] =
                new PropertyRule(
                    $property->getName(),
                    $this->getJsonName($property),
                    $this->getFieldFormatClass($property)
                );

        }
        return $metadataProperties;
    }

    /**
     * Get processing class for field value
     */
    public function getFieldFormatClass(\ReflectionProperty $property): ?string
    {
        if (!is_null($attributeFormatClass = $this->getAttributeFormatClass($property))) {
            return $attributeFormatClass;
        }
        return null;
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
                if(is_array($interfaces) && in_array(Format::class, $interfaces)){
                    return $formatterClass;
                }
                throw new InvalidFormatClassException();
            }
        }
        return null;
    }

    public function hasJsonIgnoreAttribute(\ReflectionProperty $property): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === JsonIgnore::class) {
                return true;
            }
        }
        return false;
    }

    public function getJsonName(\ReflectionProperty $property): ?string
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === JsonProperty::class) {
                return $attribute->getArguments()[0];
            }
        }
        return null;
    }


    public function isIgnored(\ReflectionProperty $property): bool
    {
        if ($property->isPrivate() || $property->isProtected()) {
            return true;
        }

        return $this->hasJsonIgnoreAttribute($property);
    }
}
