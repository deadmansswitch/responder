<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\DependencyInjection;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use DeadMansSwitch\Responder\Util\ClassnameHelper;
use DeadMansSwitch\Responder\Service\Responder\JsonResponder;
use DeadMansSwitch\Responder\Service\Responder\ResponderInterface;

final class RespondersCompilerPass implements CompilerPassInterface
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        $mapping = [];

        $path = $container->getParameter('kernel.project_dir');

        $files = (new Finder())
            ->in($path . "/src")
            ->files()
            ->name('*.php')
        ;

        foreach ($files as $file) {
            $fqcn = ClassnameHelper::getFqcnFromSplFileInfo($file);
            if ($fqcn === null) {
                continue;
            }

            $ref = new ReflectionClass($fqcn);
            if (!$ref->implementsInterface(ResponderInterface::class)) {
                continue;
            }

            $contentType = $ref
                ->getMethod('getContentType')
                ->invoke(null);

            $mapping[$contentType] = $fqcn;
        }

        if (!array_key_exists('application/json', $mapping)) {
            $mapping['application/json'] = JsonResponder::class;
        }

        if (!array_key_exists('*/*', $mapping)) {
            $mapping['*/*'] = JsonResponder::class;
        }

        $container->setParameter('dead_mans_switch.responder.mapping', $mapping);
    }
}