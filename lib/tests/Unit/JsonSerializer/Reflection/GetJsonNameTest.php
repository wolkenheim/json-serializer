<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer\Reflection;

use Wolkenheim\JsonSerializer\Reflection\ReflectionPropertyMapper;

class GetJsonNameTest extends ReflectionPropertyMapperBase
{
    /**
     * @test
     * @testdox get default name for public property
     */
    public function getDefaultNameForPublicProperty() : void
    {
        $property = $this->properties[0];
        $this->assertEquals('name', $property->getName());

        $this->assertEquals(
            'name',
            (new ReflectionPropertyMapper())->getJsonName($property)
        );
    }

    /**
     * @test
     * @testdox get name from attribute for annotated property
     */
    public function getAttributeNameForPublicProperty() : void
    {
        $property = $this->properties[3];
        $this->assertEquals('differentName', $property->getName());

        $this->assertEquals(
            'different_name',
            (new ReflectionPropertyMapper())->getJsonName($property)
        );
    }

}
