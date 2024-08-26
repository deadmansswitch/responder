<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\Exception\ClassNotFoundException;
use DeadMansSwitch\Responder\Exception\ResponderNotFoundException;
use DeadMansSwitch\Responder\Service\Responder\JsonResponder;
use DeadMansSwitch\Responder\Service\Responder\ResponderFactory;
use Symfony\Component\HttpFoundation\AcceptHeader;

test('Accept Header in mapping will return Responder instance', function () {
    $mapping = ['application/json' => JsonResponder::class];
    $factory = new ResponderFactory($mapping);
    $header  = AcceptHeader::fromString('application/json');

    $responder = $factory->createResponder($header);

    expect($responder)->toBeInstanceOf(JsonResponder::class);
});

test('Accept Header not in mapping will throw exception', function () {
    $mapping = ['application/json' => JsonResponder::class];
    $factory = new ResponderFactory($mapping);
    $header  = AcceptHeader::fromString('application/xml');

    $factory->createResponder($header);
})->expectException(ResponderNotFoundException::class);

test('Accept Header contains dummy value will throw exception', function () {
    $mapping = ['application/json' => JsonResponder::class];
    $factory = new ResponderFactory($mapping);
    $header  = AcceptHeader::fromString('foo&bar:baz123!**');

    $factory->createResponder($header);
})->expectException(ResponderNotFoundException::class);

test('Accept Header is not provided will throw exception', function () {
    $mapping = ['application/json' => JsonResponder::class];
    $factory = new ResponderFactory($mapping);
    $header  = AcceptHeader::fromString(null);

    $factory->createResponder($header);
})->expectException(ResponderNotFoundException::class);

test('Invalid class in mapping will throw exception', function () {
    $mapping = ['application/json' => 'InvalidClass'];
    $factory = new ResponderFactory($mapping);
    $header  = AcceptHeader::fromString('application/json');

    $factory->createResponder($header);
})->expectException(ClassNotFoundException::class);
