<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Domain;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;

class User
{
    public function __construct(
        public string    $name,
        protected string $hidden,
        #[JsonIgnore]
        public string $anotherName = "ignored name"
    )
    {
    }
}
