<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Exception;

use InvalidArgumentException;

final class ResponderNotFoundException extends InvalidArgumentException
{
    public function __construct(string $method)
    {
        parent::__construct("No responder found for accept header: {$method}");
    }
}