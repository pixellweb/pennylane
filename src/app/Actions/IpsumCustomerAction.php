<?php

namespace PixellWeb\Pennylane\app\Actions;


use Ipsum\Reservation\app\Models\Reservation\Facture;
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

    public function syncToProvider(Facture $facture): CustomerData
    {
        if ($facture->client->is_entreprise) {
            $saveCustomerData = SaveCompagnyCustomerData::fromIpsum($facture->client);

            $is_create = !$facture->client->reference_externe;

            if ($is_create) {
                $customer = $this->customer->createCompany($saveCustomerData);

                $facture->client->reference_externe = $customer->id;
                $facture->client->save();

            } else {
                $customer = $this->customer->updateCompany($saveCustomerData, $facture->client->reference_externe);
            }
        } else {
            $saveCustomerData = SaveIndividualCustomerData::fromIpsum($facture);

            $is_create = !$facture->client->reference_externe;

            if ($is_create) {
                $customer = $this->customer->createIndividual($saveCustomerData);

                $facture->client->reference_externe = $customer->id;
                $facture->client->save();

            } else {
                $customer = $this->customer->updateIndividual($saveCustomerData, $facture->client->reference_externe);
            }
        }


        return $customer;
    }

}


