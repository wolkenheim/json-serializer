<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Reflection;

use Wolkenheim\JsonSerializer\Attributes\JsonIgnore;

class ReflectionMethodMapper extends ReflectionMapperBase
{
    public function isIgnored(\ReflectionMethod $method): bool
    {
        if (
            $method->isProtected()
            || $method->isPrivate()
            || $method->isStatic()
            || !$this->isGetter($method->getName())
        ) return true;

        foreach ($method->getAttributes() as $attribute) {
            if ($attribute->getName() === JsonIgnore::class) {
                return true;
            }
        }

        return false;
    }

    public function isGetter(string $name): bool
    {
        if (str_starts_with($name, "get")) {
            return true;
        }
        return false;
    }

    public function defaultGetterJsonName(string $name): string
    {
        return lcfirst(substr($name, 3, strlen($name)));
    }

    public function getName(\ReflectionMethod $method): string
    {
        return $method->getName();
    }

    public function getJsonName(\ReflectionMethod $method): string
    {
        $orgName = $method->getName();

        if ($this->isGetter($orgName)) {
            return $this->defaultGetterJsonName($orgName);
        }

        return $orgName;
    }

    public function getFieldFormatClass(\ReflectionMethod $method): ?string
    {
        return $this->getDefaultValueFormatClass($method->getReturnType());
    }
}
