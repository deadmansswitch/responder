<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\EventListener\ActionResponseListener;
use DeadMansSwitch\Responder\Service\Responder\ResponderFactoryInterface;
use DeadMansSwitch\Responder\Service\StatusCodeResolver\StatusCodeResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

test("Happy path", function () {
    $factory  = $this->kernel->getContainer()->get(ResponderFactoryInterface::class);
    $resolver = $this->kernel->getContainer()->get(StatusCodeResolverInterface::class);

    $result = new class() {
        public int $id = 1;
        public string $name = "Foo";
    };

    $request = new Request();
    $request->headers->set('accept', 'application/json');
    $request->attributes->set('_controller', 'Controller\\That\\Not\\InMapping::handle');

    $event = new ViewEvent(
        kernel: $this->kernel,
        request: $request,
        requestType: $this->kernel::MAIN_REQUEST,
        controllerResult: $result,
    );

    $listener = new ActionResponseListener(
        factory: $factory,
        resolver: $resolver,
    );

    $listener($event);

    expect($event->getResponse())
        ->toBeInstanceOf(Response::class)
        ->and($event->getResponse()->getStatusCode())->toBe(200)
        ->and($event->getResponse()->getContent())->toBeJson()
    ;
});
