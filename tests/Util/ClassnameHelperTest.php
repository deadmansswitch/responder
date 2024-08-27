<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use DeadMansSwitch\Responder\Util\ClassnameHelper;

test('Get FQCN from SplFileInfo method returns the correct FQCN', function () {
    $files = (new Finder())
        ->in(__DIR__ . '/../Resources/Fixtures')
        ->name('ExampleClass.php')
    ;

    $files = iterator_to_array($files);
    $files = array_values($files);
    expect($files)->toHaveCount(1);

    $file = $files[0];
    expect($file)->toBeInstanceOf(SplFileInfo::class);

    $fqcn = ClassnameHelper::getFqcnFromSplFileInfo($file);
    expect($fqcn)->toBe('DeadMansSwitch\Responder\Tests\Resources\Fixtures\ExampleClass');
});

test('Get FQCN from SplFileInfo method returns null if no FQCN is found in class', function () {
    $files = (new Finder())
        ->in(__DIR__ . '/../Resources/Fixtures')
        ->name('ExampleClassWithoutNamespace.php')
    ;

    $files = iterator_to_array($files);
    $files = array_values($files);
    expect($files)->toHaveCount(1);

    $file = $files[0];
    expect($file)->toBeInstanceOf(SplFileInfo::class);

    $fqcn = ClassnameHelper::getFqcnFromSplFileInfo($file);
    expect($fqcn)->toBeNull();
});

test('Extract Namespace method returns the correct namespace', function () {
    $filepath = realpath(__DIR__ . '/../Resources/Fixtures/ExampleClass.php');
    expect($filepath)->toBeFile();

    $namespace = ClassnameHelper::extractNamespace($filepath);
    expect($namespace)->toBe('DeadMansSwitch\Responder\Tests\Resources\Fixtures');
});

test('Extract Namespace method returns null if no namespace is found in class', function () {
    $filepath = realpath(__DIR__ . '/../Resources/Fixtures/ExampleClassWithoutNamespace.php');
    expect($filepath)->toBeFile();

    $namespace = ClassnameHelper::extractNamespace($filepath);
    expect($namespace)->toBeNull();
});