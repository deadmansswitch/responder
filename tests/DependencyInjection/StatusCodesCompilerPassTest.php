<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\DependencyInjection\StatusCodesCompilerPass;
use DeadMansSwitch\Responder\Tests\Resources\Fixtures\src\DummyActionWithCustomResponseStatusCode;
use Symfony\Component\DependencyInjection\ContainerBuilder;

test('Default status codes registered in container', function () {
    $containerBuilder = Mockery::mock(ContainerBuilder::class);

    $containerBuilder
        ->shouldReceive('getParameter')
        ->with('kernel.project_dir')
        ->andReturn(realpath(__DIR__ . '/../../'))
    ;

    $containerBuilder
        ->shouldReceive('setParameter')
        ->withArgs(['dead_mans_switch.responder.default_status_codes', [
            'GET'    => 200,
            'POST'   => 201,
            'DELETE' => 204,
            'PUT'    => 200,
            'PATCH'  => 200,
        ]])
    ;

    $containerBuilder
        ->expects('setParameter')
        ->withAnyArgs()
        ->once()
    ;

    $pass = new StatusCodesCompilerPass();
    $pass->process($containerBuilder);
});

test('Custom status codes registered in container', function () {
    $fixturesPath     = realpath(__DIR__ . '/../Resources/Fixtures');
    $containerBuilder = Mockery::mock(ContainerBuilder::class);

    $containerBuilder
        ->shouldReceive('getParameter')
        ->with('kernel.project_dir')
        ->andReturn($fixturesPath)
    ;

    $containerBuilder
        ->shouldReceive('setParameter')
        ->withArgs(['dead_mans_switch.responder.custom_status_codes', [
            DummyActionWithCustomResponseStatusCode::class => 418,
        ]])
        ->once()
    ;

    $containerBuilder
        ->shouldReceive('setParameter')
        ->withAnyArgs()
        ->once()
    ;

    $pass = new StatusCodesCompilerPass();
    $pass->process($containerBuilder);
});