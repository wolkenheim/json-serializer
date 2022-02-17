<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Normalizer;

use Wolkenheim\JsonSerializer\Exception\TypeNotObjectException;


class ObjectNormalizer implements Normalize
{
    /**
     * @throws TypeNotObjectException
     */
    public function normalize(mixed $data): array
    {
        if (!is_object($data)) {
            throw new TypeNotObjectException();
        }

        return (array) $data;
    }

}
