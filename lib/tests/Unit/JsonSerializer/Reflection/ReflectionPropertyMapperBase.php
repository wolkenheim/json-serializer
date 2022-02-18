<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Reflection;

use PHPUnit\Framework\TestCase;
use Tests\TestHelper\Factory\UserFactory;

class ReflectionPropertyMapperBase extends TestCase
{
    /** @var array|\ReflectionProperty[]  */
    protected array $properties;

    public function setUp(): void
    {
        $user = UserFactory::make();
        $class = new \ReflectionClass($user);
        $this->properties = $class->getProperties();

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->properties = [];
        parent::tearDown();
    }
}
