<?php

namespace PixellWeb\Pennylane\app\Ressources;


class Me extends Ressource
{

    public function get(): array
    {

        return $this->crawler->get('me');

    }
}