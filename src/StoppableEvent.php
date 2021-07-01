<?php

declare(strict_types=1);

namespace Pollen\Event;

class StoppableEvent implements StoppableEventInterface
{
    /**
     * Event stopped propagation indicator.
     * @var bool
     */
    protected bool $stopPropagation = false;

    /**
     * @inheritDoc
     */
    public function stopPropagation(): StoppableEventInterface
    {
        $this->stopPropagation = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopPropagation;
    }
}