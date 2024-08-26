<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Service\Responder;

use Symfony\Component\HttpFoundation\AcceptHeaderItem;
use Symfony\Component\HttpFoundation\Response;

interface ResponderInterface
{
    public function supports(AcceptHeaderItem $header): bool;

    public function respond(mixed $data, int $status): Response;

    public static function getContentType(): string;
}