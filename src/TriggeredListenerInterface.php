<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface TriggeredListenerInterface extends ContainerProxyInterface
{
    public function __invoke(TriggeredEvent $event): void;
}