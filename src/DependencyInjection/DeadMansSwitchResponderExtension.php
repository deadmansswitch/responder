<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\DependencyInjection;

use DeadMansSwitch\Responder\EventListener\ActionResponseListener;
use DeadMansSwitch\Responder\Service\Responder\{ResponderFactory, ResponderFactoryInterface};
use DeadMansSwitch\Responder\Service\StatusCodeResolver\{StatusCodeResolver, StatusCodeResolverInterface};
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\{Parameter, Reference, ContainerBuilder};
use Symfony\Component\DependencyInjection\Extension\Extension;

final class DeadMansSwitchResponderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $container
            ->register('dead_mans_switch.responder.factory', ResponderFactory::class)
            ->setArgument('$mapping', new Parameter('dead_mans_switch.responder.mapping'))
        ;
        $container
            ->setAlias(ResponderFactoryInterface::class, 'dead_mans_switch.responder.factory')
            ->setPublic(true)
        ;

        $container
            ->register('dead_mans_switch.responder.status_code_resolver', StatusCodeResolver::class)
            ->setArgument('$customStatusCodes', new Parameter('dead_mans_switch.responder.custom_status_codes'))
            ->setArgument('$defaultStatusCodes', new Parameter('dead_mans_switch.responder.default_status_codes'))
        ;
        $container
            ->setAlias(StatusCodeResolverInterface::class, 'dead_mans_switch.responder.status_code_resolver')
            ->setPublic(true)
        ;

        $container
            ->register('dead_mans_switch.responder.action_response_listener', ActionResponseListener::class)
            ->setArgument('$factory', new Reference('dead_mans_switch.responder.factory'))
            ->setArgument('$resolver', new Reference('dead_mans_switch.responder.status_code_resolver'))
            ->addTag('kernel.event_listener', ['event' => KernelEvents::VIEW])
        ;
    }
}