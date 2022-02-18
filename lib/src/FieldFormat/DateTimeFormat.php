<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\FieldFormat;

use DateTimeInterface;

class DateTimeFormat implements Format
{
    public function format(mixed $value): string
    {
        return $value->format(DateTimeInterface::ISO8601);
    }
}
