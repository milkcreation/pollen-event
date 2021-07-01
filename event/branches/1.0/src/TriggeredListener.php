<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Support\Proxy\ContainerProxy;
use RuntimeException;

class TriggeredListener implements TriggeredListenerInterface
{
    use ContainerProxy;

    /**
     * Listener callback.
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

    /**
     * @inheritDoc
     */
    public function __invoke(TriggeredEvent $event): void
    {
        $callable = $this->resolveCallable();
        $args = $event->eventArgs();

        $callable($event, ...$args);
    }

    /**
     * Resolve the callable.
     *
     * @return callable|null
     */
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
            $callable = [$this->resolveCallableInstance($callable[0]), $callable[1]];
        }

        if (is_string($callable)) {
            $callable = $this->resolveCallableInstance($callable);
        }

        if (!is_callable($callable)) {
            throw new RuntimeException('Could not resolve a callable Triggered Listener');
        }

        return $callable;
    }

    /**
     * Resolve a callable instance from the dependency injection container or by calling a new class instance.
     *
     * @param string $classname
     *
     * @return mixed
     */
    protected function resolveCallableInstance(string $classname): object
    {
        if (($container = $this->getContainer()) && $container->has($classname)) {
            return $container->get($classname);
        }

        if (class_exists($classname)) {
            return new $classname();
        }

        throw new RuntimeException(sprintf('Triggered listener class [%s] could not be resolved.', $classname));
    }
}