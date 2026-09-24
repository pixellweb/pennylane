<?php

namespace PixellWeb\Pennylane\app\Ressources;


use PixellWeb\Pennylane\app\Data\Requests\SaveCompagnyCustomerData;
use PixellWeb\Pennylane\app\Data\Responses\CustomerData;
use PixellWeb\Pennylane\app\Data\Requests\SaveIndividualCustomerData;

class Customer extends Ressource
{

    public function list(): \Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
    {
        $customers = $this->crawler->get('customers');

        return CustomerData::collection($customers['items']);
    }

    public function get(int $id): CustomerData
    {
        $customer = $this->crawler->get('customers/'.$id);

        return CustomerData::from($customer);
    }

    public function createIndividual(SaveIndividualCustomerData $customer_data): CustomerData
    {
        $customer = $this->crawler->post('individual_customers', $customer_data->toArray());

        return CustomerData::from($customer);
    }

    public function updateIndividual(SaveIndividualCustomerData $customer_data, int $customer_id): CustomerData
    {
        $customer = $this->crawler->put('individual_customers/'.$customer_id, $customer_data->toArray());

        return CustomerData::from($customer);
    }

    public function createCompany(SaveCompagnyCustomerData $customer_data): CustomerData
    {
        $customer = $this->crawler->post('company_customers', $customer_data->toArray());

        return CustomerData::from($customer);
    }

    public function updateCompany(SaveCompagnyCustomerData $customer_data, int $customer_id): CustomerData
    {
        $customer = $this->crawler->put('company_customers/'.$customer_id, $customer_data->toArray());

        return CustomerData::from($customer);
    }
}