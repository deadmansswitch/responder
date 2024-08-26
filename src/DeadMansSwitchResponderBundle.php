<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use DeadMansSwitch\Responder\DependencyInjection\RespondersCompilerPass;
use DeadMansSwitch\Responder\DependencyInjection\StatusCodesCompilerPass;

final class DeadMansSwitchResponderBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new StatusCodesCompilerPass());
        $container->addCompilerPass(new RespondersCompilerPass());
    }
}