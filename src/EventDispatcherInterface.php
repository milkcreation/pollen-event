<?php

declare(strict_types=1);

namespace Pollen\Event;

interface EventDispatcherInterface
{
    /**
     * Appel des méthodes du répartiteur d'événement délégué.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments);

    /**
     * Déclaration d'un observateur d'événement de déclenchement.
     *
     * @param string $name
     * @param string|callable $listener
     * @param int $priority
     *
     * @return void
     */
    public function on(string $name, $listener, int $priority = 0): void;

    /**
     * Déclaration d'un observateur unique d'événement de déclenchement.
     *
     * @param string $name
     * @param string|callable $listener
     * @param int $priority
     *
     * @return void
     */
    public function one(string $name, $listener, int $priority = 0): void;

    /**
     * Exécution des traitements et comportements associés à l'événement de déclenchement.
     *
     * @param string $event
     * @param array $args
     *
     * @return TriggeredEventInterface
     */
    public function trigger(string $event, array $args = []): TriggeredEventInterface;
}