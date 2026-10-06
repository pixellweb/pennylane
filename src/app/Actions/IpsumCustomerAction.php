<?php

namespace PixellWeb\Pennylane\app\Actions;


use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use PixellWeb\Pennylane\app\Data\Requests\SaveCompagnyCustomerData;
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
        if ($reservation->entreprise) {
            $saveCustomerData = SaveCompagnyCustomerData::fromIpsum($reservation);

            $is_create = !$reservation->entreprise->reference_externe;

            if ($is_create) {
                $customer = $this->customer->createCompany($saveCustomerData);

                $reservation->entreprise->reference_externe = $customer->id;
                $reservation->entreprise->save();

            } else {
                $customer = $this->customer->updateCompany($saveCustomerData, $reservation->entreprise->reference_externe);
            }
        } else {
            $saveCustomerData = SaveIndividualCustomerData::fromIpsum($reservation);

            $is_create = !$reservation->client->reference_externe;

            if ($is_create) {
                $customer = $this->customer->createIndividual($saveCustomerData);

                $reservation->client->reference_externe = $customer->id;
                $reservation->client->save();

            } else {
                $customer = $this->customer->updateIndividual($saveCustomerData, $reservation->client->reference_externe);
            }
        }


        return $customer;
    }

}


