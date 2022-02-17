<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Domain;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;

class User
{
    public function __construct(
        public string    $name,
        protected string $hidden,
                         #[JsonIgnore]
        public string $anotherName,
                         #[JsonProperty("different_name")]
        public string $differentName,
    )
    {
    }
}
