<?php

namespace PixellWeb\Pennylane\app\Console\Commands;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Models\Client;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use mysql_xdevapi\Exception;
use PixellWeb\Pennylane\app\Actions\IpsumInvoiceAction;
use PixellWeb\Pennylane\app\Data\Responses\CustomerData;
use PixellWeb\Pennylane\app\Actions\IpsumCustomerAction;
use PixellWeb\Pennylane\app\Ressources\Customer;
use PixellWeb\Pennylane\app\Ressources\Invoice;
use PixellWeb\Pennylane\app\Ressources\Me;


class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pennylane:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test pennylane';

    protected $browser;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws EntryNotFoundException
     */
    public function handle(
        Invoice $invoice,
        Customer $customer,
        IpsumCustomerAction $ipsumCustomerAction,
        IpsumInvoiceAction $ipsumInvoiceAction,
    ): void
    {
        //dd($customer->list());
        //dd($invoice->list());
        //dd($invoice->get(30342164013056)->toArray());


        try {

            $ipsumCustomerAction->syncToProvider(Reservation::find(25));

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

dd('ok');

        $facture = $ipsumInvoiceAction->syncToProvider(Reservation::find(1));
        dd($facture);

        dd($ipsumCustomerAction->syncToProvider(Client::find(11)));

        dd($ipsumInvoiceAction->sendToCustomer(Facture::find(10), 'b.simon@pixellweb.com'));



        $facture = $ipsumInvoiceAction->updateOrCreateToIpsum($invoice->get(28436665389056));
        dd($facture);

        /*$facture_data = $invoice->get(30067000860672);
        $facture = Facture::updateOrCreate([
            'provider' => 'pennylane',
            'provider_reference' => $facture_data->id
            ],
            $facture_data->toIpsum()
        );
        dd($facture->toArray());*/


        //dd($customer->list());
        $customer_data = $customer->get(1452388286464);
        //dd($customer_data->toArray());

        $client = $ipsumCustomerAction->updateOrCreate($customer_data);
        dd($client);


        //$client = $customer_mapper->updateOrCreate($customer_data->toIpsum()->toArray());
        dd($client);


        $customaer_data  = CustomerData::from(Client::find(10));
        dd($customer->create($customaer_data));

        dd($customer_mapper->get(Client::first()));
        $customer->create($customer_mapper->get(Client::first()));

        dd($customer->get(1497117798400));
        dd($customer->list());

        $invoice = new Invoice();
        dd($invoice->list());

        $data = '{"billing_address":{"address":"8 rue de la paix","postal_code":"75002","city":"Paris","country_alpha2":"FR"},"payment_conditions":"upon_receipt","first_name":"john","last_name":"dddd","phone":"+33612345678","external_reference":"REF-1234","emails":["hello@example.org"]}';
        $customer = new Customer();
        dd($customer->create(json_decode($data, true)));




        $me = new Me();
        dd($me->get());

    }





}
