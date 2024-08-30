<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Tests\Resources\Fixtures\src;

use DeadMansSwitch\Responder\Attribute\HttpResponseCode;

#[HttpResponseCode(status: 418)]
class DummyActionWithCustomResponseStatusCode
{
    public function __invoke(): void
    {
        // Dummy fixtures
    }
}