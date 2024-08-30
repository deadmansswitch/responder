<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class TestCase extends BaseTestCase
{
    public ?KernelInterface $kernel;
    public ?ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel = new TestingKernel(environment: 'test', debug: true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->kernel->shutdown();
        $this->kernel = null;
        $this->container = null;
    }
}