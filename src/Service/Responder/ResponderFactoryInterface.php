<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Service\Responder;

use Symfony\Component\HttpFoundation\AcceptHeader;

interface ResponderFactoryInterface
{
    public function createResponder(AcceptHeader $header): ResponderInterface;
}