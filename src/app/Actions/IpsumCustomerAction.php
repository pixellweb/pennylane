<?php

namespace PixellWeb\Pennylane\app\Actions;


use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use PixellWeb\Pennylane\app\Data\Requests\SaveIndividualCustomerData;
use PixellWeb\Pennylane\app\Data\Responses\CustomerData;
use PixellWeb\Pennylane\app\Ressources\Customer;

class IpsumCustomerAction
{
    public function __construct(
        private Customer $customer,
    ) {}

    /*public function syncFromProvider(CustomerData $customer_data): Client
    {
        $client = Client::updateOrCreate([
                'code' => $customer_data->external_reference
            ],
            $customer_data->toIpsum()
        );
        if ($client->wasRecentlyCreated) {
            // Mise à jour pennylane code client
            $this->customer->update(SaveCustomerData::from($client));
        }

        return $client;
    }*/

    public function syncToProvider(Reservation $reservation): CustomerData
    {
        // TODO switch client entreprise

        $saveCustomerData = SaveIndividualCustomerData::fromIpsum($reservation);

        $pennylane_id = $reservation->client?->custom_fields->pennylane_id ?? $reservation->custom_fields->pennylane_customer_id;
        $is_create = !$pennylane_id;

        if ($is_create) {
            $customer = $this->customer->createIndividual($saveCustomerData);

            if ($reservation->client) {
                $reservation->client->custom_fields->pennylane_id = $customer->id;
                $reservation->client->save();
            } else {
                $reservation->custom_fields->pennylane_customer_id = $customer->id;
                $reservation->save();
            }

        } else {
            $customer = $this->customer->updateIndividual($saveCustomerData, $pennylane_id);
        }

        return $customer;
    }

}


