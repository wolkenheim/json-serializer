<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;

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
                );

        }
        return $metadataProperties;
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
