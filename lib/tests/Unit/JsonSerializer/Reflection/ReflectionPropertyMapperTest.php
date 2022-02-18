<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer;

use PHPUnit\Framework\TestCase;
use Tests\TestHelper\Factory\UserFactory;
use Wolkenheim\JsonSerializer\FieldFormat\DateTimeFormat;
use Wolkenheim\JsonSerializer\FieldFormat\EnumFormat;
use Wolkenheim\JsonSerializer\Reflection\ReflectionPropertyMapper;

class ReflectionPropertyMapperTest extends TestCase
{

    /**
     * @test
     * @testdox should reflect DateTimeFormat for DateTime when no attribute is set
     */
    public function dateTimeReflectionSuccess() : void {
        $user = UserFactory::make();
        $class = new \ReflectionClass($user);
        $properties = $class->getProperties();

        $dateReflectedProperty = $properties[5];
        $this->assertEquals('createdAt', $dateReflectedProperty->getName());


        $result = (new ReflectionPropertyMapper())->getFieldFormatClass($dateReflectedProperty);
        $this->assertEquals(DateTimeFormat::class, $result);
    }

    /**
     * @test
     * @testdox should reflect DateTimeFormatter for property with type DateTime
     */
    public function enumReflectionSuccess() : void {
        $user = UserFactory::make();
        $class = new \ReflectionClass($user);
        $properties = $class->getProperties();

        $statusReflectedProperty = $properties[4];
        $this->assertEquals('status', $statusReflectedProperty->getName());

        $result = (new ReflectionPropertyMapper())->getFieldFormatClass($statusReflectedProperty);
        $this->assertEquals(EnumFormat::class, $result);
    }
}
