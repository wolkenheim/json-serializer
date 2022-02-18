<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

use Wolkenheim\JsonSerializer\Exception\InvalidFormatClassException;
use Wolkenheim\JsonSerializer\Reflection\ReflectionMethodMapper;
use Wolkenheim\JsonSerializer\Reflection\ReflectionPropertyMapper;

class PropertyRuleMapper
{
    public function __construct(
        protected object $data,
    )
    {
    }

    /**
     * @throws InvalidFormatClassException
     */
    public function getRules(): array
    {
        return $this->extractRules(new \ReflectionClass($this->data));
    }

    /**
     * @return PropertyRule[]
     * @throws InvalidFormatClassException
     */
    protected function extractRules(\ReflectionClass $class): array
    {
        $reflectionPropertyMapper = new ReflectionPropertyMapper();
        $reflectionMethodMapper = new ReflectionMethodMapper();

        $metadataProperties = [];
        foreach ($class->getProperties() as $property) {
            if($reflectionPropertyMapper->isIgnored($property)){
                continue;
            }
            $metadataProperties[] =
                new PropertyRule(
                    $reflectionPropertyMapper->getName($property),
                    $reflectionPropertyMapper->getJsonName($property),
                    $reflectionPropertyMapper->getFieldFormatClass($property),
                    PropertyType::PROPERTY
                );

        }

        foreach ($class->getMethods() as $method) {
            if($reflectionMethodMapper->isIgnored($method)){
                continue;
            }
            $metadataProperties[] =
                new PropertyRule(
                    $reflectionMethodMapper->getName($method),
                    $reflectionMethodMapper->getJsonName($method),
                    $reflectionMethodMapper->getFieldFormatClass($method), // @todo: what about the return type of method? e.g. Date
                    PropertyType::METHOD
                );

        }
        return $metadataProperties;
    }
}
