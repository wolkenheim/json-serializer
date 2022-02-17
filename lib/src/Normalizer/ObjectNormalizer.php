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
            $normalized[$propertyRule->name] = $data->{$propertyRule->name};
        }
        return $normalized;
    }

}
