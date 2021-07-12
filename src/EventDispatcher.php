<?php

declare(strict_types=1);

namespace Pollen\Event;

use BadMethodCallException;
use Exception;
use League\Event\EventDispatcher as BaseEventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use Psr\EventDispatcher\ListenerProviderInterface;
use Throwable;

/**
 * @mixin BaseEventDispatcher
 */
class EventDispatcher implements EventDispatcherInterface
{
    use ConfigBagAwareTrait;
    use ContainerProxy;
    use ParamsBagAwareTrait;

    /**
     * Event dispatcher main instance.
     * @var static|null
     */
    private static ?EventDispatcherInterface $instance = null;

    /**
     * Delegate event dispatcher.
     * @var BaseEventDispatcher
     */
    protected BaseEventDispatcher $delegateDispatcher;

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

        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->listenerProvider = new PrioritizedListenerRegistry();
        $this->delegateDispatcher = new BaseEventDispatcher($this->listenerProvider);

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Get event dispatcher main instance.
     *
     * @return static
     */
    public static function getInstance(): EventDispatcherInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * Delegate dispatcher method call.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->delegateDispatcher->{$method}(...$arguments);
        } catch (Exception $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new BadMethodCallException(
                sprintf(
                    'Delegate [%s] method call [%s] throws an exception: %s',
                    BaseEventDispatcher::class,
                    $method,
                    $e->getMessage()
                ), 0, $e
            );
        }
    }

    /**
     * Get triggered event listener instance.
     *
     * @param string|callable|TriggeredListenerInterface $listener
     *
     * @return TriggeredListenerInterface
     */
    protected function getTriggeredListener($listener): TriggeredListenerInterface
    {
        $triggeredListener = $listener instanceof TriggeredListenerInterface
             ? $listener : new TriggeredListener($listener);

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
        $triggeredListener = $this->getTriggeredListener($listener);

        $this->subscribeTo($name, $triggeredListener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function one(string $name, $listener, int $priority = 0): void
    {
        $triggeredListener = $this->getTriggeredListener($listener);

        $this->subscribeOnceTo($name, $triggeredListener, $priority);
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