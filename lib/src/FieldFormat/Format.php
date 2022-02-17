<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\FieldFormat;

interface Format
{
    public function format(mixed $value) : mixed;
}
