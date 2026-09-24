<?php

namespace PixellWeb\Pennylane\app\Ressources;



use PixellWeb\Pennylane\app\Api;

abstract class Ressource
{

    public Api $crawler;


    /**
     * Ressource constructor.
     */
    public function __construct(?int $cache_time = null)
    {
        $this->crawler = new Api($cache_time);
    }

}
