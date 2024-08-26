<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Service\StatusCodeResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;

interface StatusCodeResolverInterface
{
    public function resolve(Request $request, ViewEvent $event): int;
}