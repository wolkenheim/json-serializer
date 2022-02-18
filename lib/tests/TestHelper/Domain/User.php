<?php
declare(strict_types=1);

namespace Tests\TestHelper\Domain;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;
use Wolkenheim\JsonSerializer\Attributes\JsonProperty;
use Wolkenheim\JsonSerializer\Attributes\JsonSerialize;
use Wolkenheim\JsonSerializer\FieldFormat\StringToUpperFormat;

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
        #[JsonSerialize(StringToUpperFormat::class)]
        public string $description
    )
    {
    }

    public function getHidden(): string
    {
        return $this->hidden;
    }

    public function getComputedDate(): \DateTime
    {
        return new \DateTime('2021-07-23');
    }

}
