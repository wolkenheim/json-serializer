<?php
declare(strict_types=1);

namespace Tests\TestHelper\Factory;

use Tests\TestHelper\Domain\User;
use Tests\TestHelper\Domain\UserStatus;

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
