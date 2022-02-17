<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer;

class JsonSerializer implements Serialize
{

    public function serialize(mixed $data): string
    {
        return json_encode($data);
    }
}
