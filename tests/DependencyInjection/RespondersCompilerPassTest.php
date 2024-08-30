<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\DependencyInjection\RespondersCompilerPass;
use DeadMansSwitch\Responder\Service\Responder\JsonResponder;
use DeadMansSwitch\Responder\Tests\Resources\Fixtures\WithCustomJsonResponder\src\Responder\CustomJsonResponder;
use DeadMansSwitch\Responder\Tests\Resources\Fixtures\WithCustomResponder\src\Responder\CustomResponder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

test('Default JsonResponder will be set for default content-types', function () {
    $container = Mockery::mock(ContainerBuilder::class);

    $container
        ->shouldReceive('getParameter')
        ->with('kernel.project_dir')
        ->andReturn(realpath(__DIR__ . '/../Resources/Fixtures/Empty'))
        ->once()
    ;

    $container
        ->shouldReceive('setParameter')
        ->with('dead_mans_switch.responder.mapping', [
            'application/json' => JsonResponder::class,
            '*/*' => JsonResponder::class,
        ])
        ->once()
    ;

    $pass = new RespondersCompilerPass();
    $pass->process($container);
});

test('Custom Responder will be registered for proper content-type', function () {
    $container = Mockery::mock(ContainerBuilder::class);

    $container
        ->shouldReceive('getParameter')
        ->with('kernel.project_dir')
        ->andReturn(realpath(__DIR__ . '/../Resources/Fixtures/WithCustomResponder'))
        ->once()
    ;

    $container
        ->shouldReceive('setParameter')
        ->with('dead_mans_switch.responder.mapping', [
            "fizz/buzz" => CustomResponder::class,
            "application/json" => JsonResponder::class,
            "*/*" => JsonResponder::class,
        ])
        ->once()
    ;

    $pass = new RespondersCompilerPass();
    $pass->process($container);
});

test('Custom JsonResponder will not be overridden with default one', function () {
    $container = Mockery::mock(ContainerBuilder::class);

    $container
        ->shouldReceive('getParameter')
        ->with('kernel.project_dir')
        ->andReturn(realpath(__DIR__ . '/../Resources/Fixtures/WithCustomJsonResponder'))
        ->once()
    ;

    $container
        ->shouldReceive('setParameter')
        ->with('dead_mans_switch.responder.mapping', [
            "application/json" => CustomJsonResponder::class,
            "*/*" => JsonResponder::class,
        ])
        ->once()
    ;

    $pass = new RespondersCompilerPass();
    $pass->process($container);
});