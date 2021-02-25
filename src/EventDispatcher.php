<?php

declare(strict_types=1);

namespace Pollen\Event;

use BadMethodCallException;
use Exception;
use League\Event\EventDispatcher as BaseEventDispatcher;
use League\Event\PrioritizedListenerRegistry;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ContainerAwareTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Psr\Container\ContainerInterface as Container;
use Psr\EventDispatcher\ListenerProviderInterface;
use RuntimeException;
use Throwable;

/**
 * @mixin BaseEventDispatcher
 */
class EventDispatcher implements EventDispatcherInterface
{
    use ConfigBagAwareTrait;
    use ContainerAwareTrait;
    use ParamsBagAwareTrait;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

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

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): EventDispatcherInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
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
                )
            );
        }
    }

    /**
     * Récupération d'une instance d'observateur d'un événement de déclenchement.
     *
     * @param string|callable|TriggeredListenerInterface $listener
     *
     * @return callable
     */
    protected function getTriggeredListener($listener): callable
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