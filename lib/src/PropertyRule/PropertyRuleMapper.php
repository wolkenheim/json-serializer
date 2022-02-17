<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;

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


    public function isIgnored(\ReflectionProperty $property): bool
    {
        if ($property->isPrivate() || $property->isProtected()) {
            return true;
        }

        return $this->hasJsonIgnoreAttribute($property);
    }
}
