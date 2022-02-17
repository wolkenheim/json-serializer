<?php
declare(strict_types=1);

namespace Wolkenheim\JsonSerializer\Exception;

class InvalidFormatClassException extends \Exception
{
    protected $message = "Class needs to implement interface";

    public function __construct()
    {
        parent::__construct($this->message);
    }


}
