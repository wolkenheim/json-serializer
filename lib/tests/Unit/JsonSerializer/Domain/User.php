<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Domain;

class User
{
    public function __construct(
        public string $name
    )
    {
    }
}
