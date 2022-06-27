<?php

namespace LuckyConsultation;

use Psr\Container\ContainerInterface;

class LuckyConsultationController
{
    /**
     * Der Konstruktor.
     *
     * @param ContainerInterface $container der Dependency Container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
