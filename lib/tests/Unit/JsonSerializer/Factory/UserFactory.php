<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Factory;

use Tests\Unit\JsonSerializer\Domain\User;
use Tests\Unit\JsonSerializer\Domain\UserStatus;

class UserFactory
{
    public static function make(): User {
        return new User(
            "Matt",
            "Berry",
            "Taika",
            "Waititi",
            UserStatus::ACTIVE,
            new \DateTime('2022-01-22')
        );
    }
}
