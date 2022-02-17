<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Normalizer;

use Symfony\Component\VarDumper\VarDumper;
use Wolkenheim\JsonSerializer\Exception\TypeNotObjectException;
use Wolkenheim\JsonSerializer\PropertyRule\PropertyRule;
use Wolkenheim\JsonSerializer\PropertyRule\PropertyRuleMapper;


class ObjectNormalizer implements Normalize
{
    /**
     * @throws TypeNotObjectException|\ReflectionException
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
            $normalized[$this->getKey($propertyRule)] = $data->{$propertyRule->name};
        }
        return $normalized;
    }

    public function getKey(PropertyRule $propertyRule): string
    {
        if (!is_null($propertyRule->jsonName)) {
            return $propertyRule->jsonName;
        }
        return $propertyRule->name;
    }

}
