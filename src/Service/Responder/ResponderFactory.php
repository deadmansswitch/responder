<?php

declare(strict_types=1);

namespace DeadMansSwitch\Responder\Service\Responder;

use DeadMansSwitch\Responder\Exception\ClassNotFoundException;
use DeadMansSwitch\Responder\Exception\ResponderNotFoundException;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;

final class ResponderFactory implements ResponderFactoryInterface
{
    public function __construct(private readonly array $mapping) {}

    /**
     * @throws ClassNotFoundException
     */
    public function createResponder(AcceptHeader $header): ResponderInterface
    {
        foreach ($header->all() as $item) {
            assert($item instanceof AcceptHeaderItem);

            if (array_key_exists($item->getValue(), $this->mapping)) {
                $class = $this->mapping[$item->getValue()];

                if (!class_exists($class)) {
                    throw new ClassNotFoundException($class);
                }

                return new $class();
            }
        }

        throw new ResponderNotFoundException("{$header}");
    }
}