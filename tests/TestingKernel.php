<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Tests;

use DeadMansSwitch\Responder\DeadMansSwitchResponderBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

final class TestingKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        yield new DeadMansSwitchResponderBundle();
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void {}
}