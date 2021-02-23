<?php

declare(strict_types=1);

namespace Pollen\Event;

interface TriggeredListenerInterface
{
    public function __invoke(TriggeredEvent $event): void;
}