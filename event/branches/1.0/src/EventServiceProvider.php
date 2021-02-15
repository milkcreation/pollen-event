<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Container\BaseServiceProvider;

class EventServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        EventManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(EventManagerInterface::class, function () {
            return new EventManager([], $this->getContainer());
        });
    }
}