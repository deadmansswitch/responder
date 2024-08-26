<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Service\StatusCodeResolver;

use DeadMansSwitch\Responder\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class StatusCodeResolver implements StatusCodeResolverInterface
{
    public function __construct(
        private readonly array $customStatusCodes,
        private readonly array $defaultStatusCodes,
    ) {}

    /**
     * @throws LogicException
     */
    public function resolve(Request $request, ViewEvent $event): int
    {
        $controller = $request->attributes->get('_controller');
        if (!is_string($controller)) {
            throw new LogicException('Request object does not have a controller set');
        }

        if (array_key_exists($controller, $this->customStatusCodes)) {
            return $this->customStatusCodes[$controller];
        }

        if ($event->getControllerResult() === null) {
            return Response::HTTP_NO_CONTENT;
        }

        $method = strtoupper($request->getMethod());
        return $this->defaultStatusCodes[$method] ?? Response::HTTP_OK;
    }
}