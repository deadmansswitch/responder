<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Tests\Resources\Fixtures\WithCustomJsonResponder\src\Responder;

use DeadMansSwitch\Responder\Service\Responder\ResponderInterface;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;
use Symfony\Component\HttpFoundation\Response;

final class CustomJsonResponder implements ResponderInterface
{
    public function supports(AcceptHeaderItem $header): bool
    {
        return true;
    }

    public function respond(mixed $data, int $status): Response
    {
        return new Response();
    }

    public static function getContentType(): string
    {
        return 'application/json';
    }
}