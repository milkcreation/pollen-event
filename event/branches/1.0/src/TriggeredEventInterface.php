<?php

namespace Pollen\Event;

use League\Event\HasEventName;

interface TriggeredEventInterface extends HasEventName
{
    /**
     * Récupération du nom de qualification de l'événement.
     *
     * @return string
     */
    public function eventName(): string;

    /**
     * Récupération de la liste des arguments associés à l'événement.
     *
     * @return array
     */
    public function eventArgs(): array;

    /**
     * Définition de la liste des arguments associés à l'événement.
     *
     * @param array $args
     *
     * @return static
     */
    public function setEventArgs(array $args): TriggeredEventInterface;
}
