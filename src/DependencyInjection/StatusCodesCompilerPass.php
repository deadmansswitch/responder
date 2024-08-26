<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\DependencyInjection;

use DeadMansSwitch\Responder\Attribute\HttpResponseCode;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\{Finder, SplFileInfo};

final class StatusCodesCompilerPass implements CompilerPassInterface
{
    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        $this->registerDefaultStatusCodes($container);
        $this->registerCustomStatusCodesForActions($container);
    }

    private function registerDefaultStatusCodes(ContainerBuilder $container): void
    {
        $container->setParameter('dead_mans_switch.responder.default_status_codes', [
            'GET'    => 200,
            'POST'   => 201,
            'DELETE' => 204,
            'PUT'    => 200,
            'PATCH'  => 200,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    private function registerCustomStatusCodesForActions(ContainerBuilder $container): void
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

            // Check for attribute assigned to class
            $classAttributes = $ref->getAttributes();
            foreach ($classAttributes as $attr) {
                if ($attr->getName() !== HttpResponseCode::class) {
                    continue;
                }

                $args   = $attr->getArguments();
                $key    = array_key_first($args);
                $status = $args[$key];

                $mapping[$fqcn] = $status;
            }

            // Check for attributes assigned to methods
            foreach ($ref->getMethods() as $method) {
                $methodAttributes = $method->getAttributes();
                foreach ($methodAttributes as $attr) {
                    if ($attr->getName() !== HttpResponseCode::class) {
                        continue;
                    }

                    $args   = $attr->getArguments();
                    $key    = array_key_first($args);
                    $status = $args[$key];
                    $action = $method->getName() === '__invoke'
                        ? $fqcn
                        : $fqcn . '::' . $method->getName();

                    $mapping[$action] = $status;
                }
            }
        }

        $container->setParameter('dead_mans_switch.responder.custom_status_codes', $mapping);
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