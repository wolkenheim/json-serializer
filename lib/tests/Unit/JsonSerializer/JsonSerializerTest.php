<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer;

use Symfony\Component\VarDumper\VarDumper;
use Tests\Unit\JsonSerializer\Domain\User;
use Wolkenheim\JsonSerializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function serializeObject() : void {
        $serializer = new JsonSerializer();
        $json = $serializer->serialize(new User("Matt"));
        VarDumper::dump($json);

        $this->assertEquals('{"name":"Matt"}', $json);
    }

}
