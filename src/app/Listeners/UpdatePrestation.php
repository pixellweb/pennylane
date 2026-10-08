<?php

namespace PixellWeb\Pennylane\app\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ipsum\Reservation\app\Events\PrestationCreatedEvent;
use Ipsum\Reservation\app\Events\PrestationUpdatedEvent;
use Alert;
use PixellWeb\Pennylane\app\Actions\IpsumProductAction;

class UpdatePrestation
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(private IpsumProductAction $ipsumProductAction)
    {
        //
    }


    public function handle(PrestationUpdatedEvent|PrestationCreatedEvent $event)
    {
        if (config('ipsum.reservation.facture_provider') !== 'pennylane') {
            return;
        }

        try {
            $this->ipsumProductAction->syncToProvider($event->prestation);
        } catch (\Exception $e) {
            $message = print_r($e->getMessage(), true);
            Alert::error("Le produit n'a pas été mis à jour sur Pennylane : $message")->flash();
        }

    }
}
