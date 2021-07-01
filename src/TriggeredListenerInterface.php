<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface TriggeredListenerInterface extends ContainerProxyInterface
{
    /**
     * Trigger the listener callable by the event dispatch.
     *
     * @param TriggeredEvent $event
     *
     * @return void
     */
    public function __invoke(TriggeredEvent $event): void;
}