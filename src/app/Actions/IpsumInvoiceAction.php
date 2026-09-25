<?php

namespace PixellWeb\Pennylane\app\Actions;


use Ipsum\Reservation\app\Enum\FactureType;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use PixellWeb\Pennylane\app\Data\Requests\CreateInvoiceData;
use PixellWeb\Pennylane\app\Data\Requests\InvoiceLinesData;
use PixellWeb\Pennylane\app\Data\Responses\InvoiceData;
use PixellWeb\Pennylane\app\Ressources\Invoice;

class IpsumInvoiceAction
{
    public function __construct(
        private Invoice $invoice
    ) {}

    public function syncFromProvider(InvoiceData $invoiceData): Facture
    {
        return Facture::updateOrCreate([
            'provider' => 'pennylane',
            'provider_reference' => $invoiceData->id
        ],
            $invoiceData->toIpsum()
        );
    }

    public function syncToProvider(Reservation $reservation): Facture
    {

        $dataCollection = [];
        foreach ($reservation->prestations as $prestation) {
            $dataCollection[] = InvoiceLinesData::fromIpsum($reservation, $prestation);
        }

        $invoice = $this->invoice->create(CreateInvoiceData::fromIpsum($reservation, $dataCollection));

        $factureType = $reservation->factureLocation ? FactureType::ADDITIONNELLE : FactureType::LOCATION;

        return Facture::create($invoice->toIpsum($factureType));
    }


    public function sendToCustomer(Facture $facture, bool|string|array $emails = null)
    {
        if ($emails) {
            if ($emails === true) {
                $emails = [];
            } else {
                $emails = is_array($emails) ? $emails : [$emails];
            }

            $this->invoice->sendByEmail($facture->provider_reference, $emails);

            return;
        }

        // Ne faire que sur les entreprises ?
        try {
            $this->invoice->sendToPA($facture->provider_reference);
        } catch (\Exception $exception) {
            if ($exception->getCode() === 422) {
                $this->sendToCustomer($facture, true);
            }
        }
    }

    public function getUrlPdf(Facture $facture): string
    {
        return $this->invoice->get($facture->provider_reference)->public_file_url;
    }

}


