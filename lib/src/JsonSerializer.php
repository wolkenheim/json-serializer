<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer;

use Wolkenheim\JsonSerializer\Exception\TypeNotObjectException;
use Wolkenheim\JsonSerializer\Normalizer\Normalize;

class JsonSerializer
{
    public function __construct(
        protected Normalize $objectNormalizer
    )
    {
    }

    public function serialize(mixed $data): string
    {
        // what is the type of $data?
        $normalizedData = $this->normalize($data);

        // use the most simple encoding strategy for now
        return json_encode($normalizedData);
    }

    /**
     * @throws TypeNotObjectException
     */
    public function normalize(mixed $data): array
    {
        if (is_object($data)) {
            return $this->objectNormalizer->normalize($data);
        }
        throw new TypeNotObjectException();
    }
}
