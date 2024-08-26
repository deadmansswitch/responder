<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Exception;

use Exception;

final class ClassNotFoundException extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct(message: "Class not found or namespace configured wrong: {$class}");
    }
}