<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\DependencyInjection\DeadMansSwitchResponderExtension;
use DeadMansSwitch\Responder\Service\Responder\ResponderFactory;
use DeadMansSwitch\Responder\Service\Responder\ResponderFactoryInterface;
use DeadMansSwitch\Responder\Service\StatusCodeResolver\StatusCodeResolver;
use DeadMansSwitch\Responder\Service\StatusCodeResolver\StatusCodeResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\KernelEvents;

test('Responder factory registered and aliased with interface', function () {
    $factory = $this
        ->kernel
        ->getContainer()
        ->get(ResponderFactoryInterface::class);

    expect($factory)
        ->toBeInstanceOf(ResponderFactoryInterface::class)
        ->and($factory)
        ->toBeInstanceOf(ResponderFactory::class);
});

test('Status Code Resolver registered and aliased with interface', function () {
    $resolver = $this
        ->kernel
        ->getContainer()
        ->get(StatusCodeResolverInterface::class);

    expect($resolver)
        ->toBeInstanceOf(StatusCodeResolverInterface::class)
        ->and($resolver)
        ->toBeInstanceOf(StatusCodeResolver::class);
});

test('Action Response Event Listener registered and tagged', function () {
    $container = new ContainerBuilder();

    $extension = new DeadMansSwitchResponderExtension();
    $extension->load([], $container);

    $def = $container->getDefinition('dead_mans_switch.responder.action_response_listener');

    expect($def)
        ->toBeInstanceOf(Definition::class)
        ->and($def->hasTag('kernel.event_listener'))
        ->and($def->getTag('kernel.event_listener'))->toBe([['event' => KernelEvents::VIEW]])
    ;
});