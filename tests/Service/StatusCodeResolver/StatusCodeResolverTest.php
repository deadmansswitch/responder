<?php

declare(strict_types=1);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use DeadMansSwitch\Responder\Service\StatusCodeResolver\StatusCodeResolver;
use DeadMansSwitch\Responder\Exception\LogicException;

test("Request without controller will throw exception", function () {
    $kernel   = Mockery::mock(KernelInterface::class);
    $request  = new Request();
    $event    = new ViewEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, null);
    $resolver = new StatusCodeResolver([], []);

    $resolver->resolve($request, $event);
})->throws(LogicException::class);

test('Custom status code will be resolved if controller exits in mapping', function () {
    $kernel   = Mockery::mock(KernelInterface::class);
    $request  = new Request();
    $request->attributes->set('_controller', 'App\Controller\MyController::myMethod');
    $request->setMethod('GET');
    $event    = new ViewEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, null);
    $resolver = new StatusCodeResolver(
        customStatusCodes: ['App\Controller\MyController::myMethod' => 418],
        defaultStatusCodes: ['GET' => 200],
    );

    $actual = $resolver->resolve($request, $event);

    expect($actual)->toBe(418);
});

test('If controller result is `null` response code will be 204', function () {
    $kernel  = Mockery::mock(KernelInterface::class);
    $request = new Request();
    $request->attributes->set('_controller', 'App\Controller\MyController::myMethod');
    $request->setMethod('DELETE');

    $event = new ViewEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, null);

    $resolver = new StatusCodeResolver(
        customStatusCodes: [],
        defaultStatusCodes: ['DELETE' => 200],
    );

    $actual = $resolver->resolve($request, $event);

    expect($actual)->toBe(204);
});

test('Default status code will be returned if custom not specified in mapping', function () {
    $request = new Request();
    $request->attributes->set('_controller', 'App\Controller\MyController::myMethod');
    $request->setMethod('POST');
    $kernel = Mockery::mock(KernelInterface::class);
    $event  = new ViewEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, ['dummy' => 'result']);

    $resolver = new StatusCodeResolver(
        customStatusCodes: [],
        defaultStatusCodes: ['POST' => 418],
    );

    $actual = $resolver->resolve($request, $event);

    expect($actual)->toBe(418);
});