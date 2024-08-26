<?php

declare(strict_types=1);

use DeadMansSwitch\Responder\DeadMansSwitchResponderBundle;
use DeadMansSwitch\Responder\DependencyInjection\RespondersCompilerPass;
use DeadMansSwitch\Responder\DependencyInjection\StatusCodesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

test('DeadMansSwitchResponderBundle extends Bundle class', function () {
    expect(new DeadMansSwitchResponderBundle())
        ->toBeInstanceOf(Bundle::class)
    ;
});

test('DeadMansSwitchResponderBundle registers compiler passes', function () {
    $containerMock = Mockery::mock(ContainerBuilder::class);

    $containerMock->expects('addCompilerPass')
        ->with(Mockery::type(StatusCodesCompilerPass::class))
        ->once();

    $containerMock->expects('addCompilerPass')
        ->with(Mockery::type(RespondersCompilerPass::class))
        ->once();

    $bundle = new DeadMansSwitchResponderBundle();
    $bundle->build($containerMock);
});