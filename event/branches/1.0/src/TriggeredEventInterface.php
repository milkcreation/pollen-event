<?php

declare(strict_types=1);

namespace Pollen\Event;

use League\Event\HasEventName;

interface TriggeredEventInterface extends HasEventName
{
    /**
     * Get the triggered event name.
     *
     * @return string
     */
    public function eventName(): string;

    /**
     * Get list of arguments passed to the associated listeners.
     *
     * @return array
     */
    public function eventArgs(): array;

    /**
     * Set list of arguments passed to the associated listeners.
     *
     * @param array $args
     *
     * @return static
     */
    public function setEventArgs(array $args): TriggeredEventInterface;
}
