<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Attribute;

use Attribute;

#[Attribute(flags: Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final class HttpResponseCode
{
    public function __construct(public int $status) {}
}