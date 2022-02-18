<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\FieldFormat;

class StringToUpperFormat implements Format
{
    public function format(mixed $value): string
    {
        return strtoupper($value);
    }

}
