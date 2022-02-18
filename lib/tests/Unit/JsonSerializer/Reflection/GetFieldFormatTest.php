<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Reflection;

use Wolkenheim\JsonSerializer\FieldFormat\DateTimeFormat;
use Wolkenheim\JsonSerializer\FieldFormat\EnumFormat;
use Wolkenheim\JsonSerializer\FieldFormat\StringToUpperFormat;
use Wolkenheim\JsonSerializer\Reflection\ReflectionPropertyMapper;

class GetFieldFormatTest extends ReflectionPropertyMapperBase
{
    /**
     * @test
     * @testdox should reflect DateTimeFormat for DateTime when no attribute is set
     */
    public function dateTimeReflectionSuccess() : void {
        $property = $this->properties[5];
        $this->assertEquals('createdAt', $property->getName());

        $this->assertEquals(
            DateTimeFormat::class,
            (new ReflectionPropertyMapper())->getFieldFormatClass($property)
        );
    }

    /**
     * @test
     * @testdox should reflect Enum for property with corresponding class EnumFormat
     */
    public function enumReflectionSuccess() : void {
        $property = $this->properties[4];
        $this->assertEquals('status', $property->getName());

        $this->assertEquals(
            EnumFormat::class,
            (new ReflectionPropertyMapper())->getFieldFormatClass($property)
        );
    }

    /**
     * @test
     * @testdox should reflect set attribute with custom string formatter
     */
    public function customAttributeReflectionSuccess() : void {
        $property = $this->properties[6];
        $this->assertEquals('description', $property->getName());

        $this->assertEquals(
            StringToUpperFormat::class,
            (new ReflectionPropertyMapper())->getFieldFormatClass($property)
        );
    }

}
