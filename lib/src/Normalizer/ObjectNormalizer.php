<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Normalizer;

use Symfony\Component\VarDumper\VarDumper;
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

        $this->reflect(new \ReflectionClass($data));

        return (array) $data;
    }

    protected function extractInformation(\ReflectionClass $class): void {
        foreach ($class->getProperties() as $property) {
         VarDumper::dump($property);
        }
    }

}
