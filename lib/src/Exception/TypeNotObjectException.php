<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Exception;

class TypeNotObjectException extends \Exception
{
    protected $message = "Type needs to be an object";

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
