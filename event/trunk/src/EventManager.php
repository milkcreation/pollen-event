<?php

declare(strict_types=1);

namespace Pollen\Event;

use Pollen\Support\Concerns\ConfigBagTrait;
use Pollen\Support\Concerns\ContainerAwareTrait;
use Pollen\Support\Concerns\ParamsBagTrait;
use Psr\Container\ContainerInterface as Container;

class EventManager implements EventManagerInterface
{
    use ConfigBagTrait;
    use ContainerAwareTrait;
    use ParamsBagTrait;

    /**
     * @var EventListenerInterface[]
     */
    protected $events = [];

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
    }

    /**
     * @inheritDoc
     */
    public function on($name, $listener, $priority = 0)
    {
        $listener = new Listener($listener);

        return $this->addListener($name, $listener, $priority);
    }

    /**
     * @inheritDoc
     */
    public function trigger($event, $args = [])
    {
        if(func_num_args() === 1) {
            $_args = func_get_args();
            $_args[] = [];
        }

        return call_user_func_array([$this, 'emit'], $_args ?? func_get_args());
    }
}