<?php

declare(strict_types=1);

namespace Pollen\Event;

use Psr\EventDispatcher\StoppableEventInterface as BaseStoppableEventInterface;

interface StoppableEventInterface extends BaseStoppableEventInterface
{
    /**
     * Activation de l'interruption de la propagation de l'événement.
     *
     * @return static
     */
    public function stopPropagation(): StoppableEventInterface;
}