<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Support\Concerns\ContainerAwareTrait;
use RuntimeException;

class TriggeredListener implements TriggeredListenerInterface
{
    use ContainerAwareTrait;

    /**
     * @var string|callable
     */
    protected $callable;

    /**
     * @param string|callable $callable
     */
    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(TriggeredEvent $event): void
    {
        $callable = $this->resolveCallable();
        $args = $event->eventArgs();

        $callable($event, ...$args);
    }

    protected function resolveCallable(): ?callable
    {
        $callable = $this->callable;

        if (is_string($callable) && strpos($callable, '::') !== false) {
            $callable = explode('::', $callable);
        }

        if (is_array($callable) && isset($callable[0]) && is_object($callable[0])) {
            $callable = [$callable[0], $callable[1]];
        }

        if (is_array($callable) && isset($callable[0]) && is_string($callable[0])) {
            $callable = [$this->resolveContainerCallable($callable[0]), $callable[1]];
        }

        if (is_string($callable)) {
            $callable = $this->resolveContainerCallable($callable);
        }

        if (!is_callable($callable)) {
            throw new RuntimeException('Could not resolve a callable Triggered Listener');
        }
        return $callable;
    }

    /**
     * Récupération de la classe de rappel.
     *
     * @param string $class
     *
     * @return mixed
     */
    protected function resolveContainerCallable(string $class): object
    {
       if (($container = $this->getContainer()) && $container->has($class)) {
            return $container->get($class);
       }

       if (class_exists($class)) {
            return new $class();
        }
        throw new RuntimeException('Triggered Listener Class unresolvable');
    }
}