<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\DependencyInjection;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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
            $fqcn = $this->getFqcnFromFileInfo($file);
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

    private function getFqcnFromFileInfo(SplFileInfo $file): string|null
    {
        $ns = $this->extractNameSpace($file->getRealPath());
        if ($ns === null) {
            return null;
        }

        $name = $file->getBasename('.php');
        $fqcn = "{$ns}\\{$name}";
        if (class_exists($fqcn) === false) {
            return null;
        }

        return $fqcn;
    }

    private function extractNameSpace(string $path): ?string
    {
        $content = file_get_contents($path);
        $tokens  = token_get_all($content);
        $count   = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if ($token[0] !== T_NAMESPACE) {
                continue;
            }

            return $tokens[$i + 2][1];
        }

        return null;
    }
}