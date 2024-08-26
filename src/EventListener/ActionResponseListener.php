<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\EventListener;

use DeadMansSwitch\Responder\Service\Responder\ResponderFactoryInterface;
use DeadMansSwitch\Responder\Service\StatusCodeResolver\StatusCodeResolverInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class ActionResponseListener
{
    private const HEADER = 'accept';

    public function __construct(
        private readonly ResponderFactoryInterface $factory,
        private readonly StatusCodeResolverInterface $resolver
    ) {}

    public function __invoke(ViewEvent $event): void
    {
        $result    = $event->getControllerResult();
        $header    = $event->getRequest()->headers->get(self::HEADER);
        $accept    = AcceptHeader::fromString($header);
        $responder = $this->factory->createResponder($accept);
        $status    = $this->resolver->resolve($event->getRequest(), $event);
        $response  = $responder->respond($result, $status);

        $event->setResponse($response);
    }
}