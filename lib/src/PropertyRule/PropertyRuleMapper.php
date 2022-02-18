<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\PropertyRule;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;
use Wolkenheim\JsonSerializer\Attributes\JsonSerialize;
use Wolkenheim\JsonSerializer\Exception\InvalidFormatClassException;
use Wolkenheim\JsonSerializer\FieldFormat\Format;
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

        $metadataProperties = [];
        foreach ($class->getProperties() as $property) {
            if ($reflectionPropertyMapper->isIgnored($property)) {
                continue;
            }
            $metadataProperties[] =
                new PropertyRule(
                    $reflectionPropertyMapper->getName($property),
                    $reflectionPropertyMapper->getJsonName($property),
                    $reflectionPropertyMapper->getFieldFormatClass($property)
                );

        }
        return $metadataProperties;
    }




}
