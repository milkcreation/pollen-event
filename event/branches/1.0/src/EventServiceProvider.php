<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Container\BaseServiceProvider;

class EventServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        EventDispatcherInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(EventDispatcherInterface::class, function () {
            return new EventDispatcher([], $this->getContainer());
        });
    }
}