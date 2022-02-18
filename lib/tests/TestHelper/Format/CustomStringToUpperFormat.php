<?php
declare(strict_types=1);

namespace Wolkenheim\TestHelper\Format;

use Wolkenheim\JsonSerializer\FieldFormat\Format;

class CustomStringToUpperFormat implements Format
{
    public function format(mixed $value): string
    {
        return strtoupper($value);
    }
}
