<?php

declare(strict_types=1);

namespace Pollen\Event;

use Psr\EventDispatcher\StoppableEventInterface as BaseStoppableEventInterface;

interface StoppableEventInterface extends BaseStoppableEventInterface
{
    /**
     * Triggers the stop of the propagation of the associated event.
     *
     * @return static
     */
    public function stopPropagation(): StoppableEventInterface;
}