<?php
declare(strict_types=1);

namespace Tests\Unit\JsonSerializer;

use PHPUnit\Framework\TestCase;
use Symfony\Component\VarDumper\VarDumper;
use Tests\Unit\JsonSerializer\Domain\User;
use Wolkenheim\JsonSerializer\JsonSerializer;
use Wolkenheim\JsonSerializer\Normalizer\ObjectNormalizer;

class JsonSerializerTest extends TestCase
{
    /** @test */
    public function serializeObject() : void {
        $serializer = new JsonSerializer(new ObjectNormalizer());
        $json = $serializer->serialize(new User("Matt"));
        VarDumper::dump($json);

        $this->assertEquals('{"name":"Matt"}', $json);
    }

}
