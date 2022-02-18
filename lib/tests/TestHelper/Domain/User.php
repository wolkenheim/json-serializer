<?php
declare(strict_types=1);

namespace Tests\TestHelper\Domain;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;
use Wolkenheim\JsonSerializer\Attributes\JsonSerialize;
use Wolkenheim\TestHelper\Format\CustomStringToUpperFormat;

class User
{
    public function __construct(
        public string    $name,
        protected string $hidden,
                         #[JsonIgnore]
        public string $anotherName,
                         #[JsonProperty("different_name")]
        public string $differentName,
        public UserStatus $status,
        public \DateTime $createdAt,
        #[JsonSerialize(CustomStringToUpperFormat::class)]
        public string $description
    )
    {
    }
}
