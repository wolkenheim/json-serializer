<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\FieldFormat;

class EnumFormat implements Format
{
    public function format(mixed $value): string
    {
        return $value->name;
    }

}
