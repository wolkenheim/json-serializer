<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Normalizer;

use Wolkenheim\JsonSerializer\Exception\InvalidFormatClassException;
use Wolkenheim\JsonSerializer\Exception\TypeNotObjectException;
use Wolkenheim\JsonSerializer\FieldFormat\Format;
use Wolkenheim\JsonSerializer\PropertyRule\PropertyRule;
use Wolkenheim\JsonSerializer\PropertyRule\PropertyRuleMapper;
use Wolkenheim\JsonSerializer\PropertyRule\PropertyType;


class ObjectNormalizer implements Normalize
{
    /**
     * @throws TypeNotObjectException|\ReflectionException|InvalidFormatClassException
     */
    public function normalize(mixed $data): array
    {
        if (!is_object($data)) {
            throw new TypeNotObjectException();
        }

        return $this->buildNormalizedArray(
            $data,
            (new PropertyRuleMapper($data))->getRules()
        );
    }

    /**
     * @param PropertyRule[] $rules
     */
    public function buildNormalizedArray(object $data, array $rules): array
    {
        $normalized = [];
        foreach ($rules as $propertyRule) {
            $normalized[$this->getKey($propertyRule)] = $this->processValue(
                $this->readValue($data, $propertyRule),
                $propertyRule
            );
        }
        return $normalized;
    }

    public function getKey(PropertyRule $propertyRule): string
    {
        return $propertyRule->jsonName;
    }

    public function readValue(object $data, PropertyRule $propertyRule): mixed
    {
        if ($propertyRule->propertyType === PropertyType::METHOD) {
            return $data->{$propertyRule->accessName}();
        }
        return $data->{$propertyRule->accessName};
    }

    public function processValue(mixed $propertyValue, PropertyRule $propertyRule): mixed
    {
        if (!empty($propertyRule->childrenRules)) {
            return $this->buildNormalizedArray($propertyValue, $propertyRule->childrenRules);
        }

        if (!is_null($propertyRule->fieldFormatClass)) {
            /** @var Format $formatter */
            $formatter = (new $propertyRule->fieldFormatClass);
            return $formatter->format($propertyValue);
        }
        return $propertyValue;
    }

}
