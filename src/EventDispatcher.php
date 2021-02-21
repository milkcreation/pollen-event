<?php

declare(strict_types=1);

namespace Pollen\Event;

use BadMethodCallException;
use League\Event\EventDispatcher as BaseEventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use Pollen\Support\Concerns\ConfigBagTrait;
use Pollen\Support\Concerns\ContainerAwareTrait;
use Pollen\Support\Concerns\ParamsBagTrait;
use Psr\Container\ContainerInterface as Container;
use Psr\EventDispatcher\ListenerProviderInterface;
use Throwable;

/**
 * @mixin BaseEventDispatcher
 */
class EventDispatcher implements EventDispatcherInterface
{
    use ConfigBagTrait;
    use ContainerAwareTrait;
    use ParamsBagTrait;

    /**
     * @var BaseEventDispatcher
     */
    protected $delegateDispatcher;

    /**
     * @var ListenerProviderInterface
     */
    protected $listenerProvider;

    /**
     * @param array $config
     * @param Container|null $container
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        $this->listenerProvider = new PrioritizedListenerRegistry();
        $this->delegateDispatcher = new BaseEventDispatcher($this->listenerProvider);
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->delegateDispatcher->{$method}(...$arguments);
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Delegate [%s] method call [%s] throws an exception: %s',
                    BaseEventDispatcher::class,
                    $method,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Récupération d'une instance d'observateur d'un événement de déclenchement.
     *
     * @param string|callable $listener
     *
     * @return callable
     */
    protected function getTriggeredListener($listener): callable
    {
        $triggeredListener = new TriggeredListener($listener);

        if ($container = $this->getContainer()) {
            $triggeredListener->setContainer($container);
        }

        return $triggeredListener;
    }

    /**
     * @inheritDoc
     */
    public function on(string $name, $listener, int $priority = 0): void
    {
        $listener = $this->getTriggeredListener($listener);

        $this->subscribeTo($name, $listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function one(string $name, $listener, int $priority = 0): void
    {
        $listener = $this->getTriggeredListener($listener);

        $this->subscribeOnceTo($name, $listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $event, array $args = []): TriggeredEventInterface
    {
        /** @var TriggeredEventInterface $e */
        $e = $this->dispatch(new TriggeredEvent($event, $args));

        return $e;
    }
}