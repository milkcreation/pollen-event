<?php

declare(strict_types=1);

namespace Pollen\Event;

class TriggeredEvent extends StoppableEvent implements TriggeredEventInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    protected $args;

    /**
     * EventTriggered constructor.
     *
     * @param string $name
     * @param array $args
     */

    public function __construct(string $name, array $args = [])
    {
        $this->name = $name;
        $this->args = $args;
    }

    /**
     * @inheritDoc
     */
    public function eventName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function eventArgs(): array
    {
        return $this->args;
    }

    /**
     * @inheritDoc
     */
    public function setEventArgs(array $args): TriggeredEventInterface
    {
        $this->args = $args;

        return $this;
    }
}
