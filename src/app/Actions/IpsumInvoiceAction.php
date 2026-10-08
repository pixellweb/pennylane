<?php

namespace PixellWeb\Pennylane\app\Actions;


use Illuminate\Support\Facades\Cache;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use PixellWeb\Pennylane\app\Data\Requests\CreateInvoiceData;
use PixellWeb\Pennylane\app\Data\Requests\UpdateInvoiceData;
use PixellWeb\Pennylane\app\Data\Responses\InvoiceData;
use PixellWeb\Pennylane\app\PennylaneException;
use PixellWeb\Pennylane\app\Ressources\Invoice;

class IpsumInvoiceAction
{
    public function __construct(
        private Invoice $invoice
    ) {}

    /*public function syncFromProvider(InvoiceData $invoiceData): Facture
    {
        return Facture::updateOrCreate([
            'provider' => 'pennylane',
            'provider_reference' => $invoiceData->id
        ],
            $invoiceData->toIpsum()
        );
    }*/

    public function syncToProvider(Facture $facture, bool $brouillon): ?InvoiceData
    {
        if ($facture->provider_reference) {

            if ($facture->provider !== 'pennylane') {
                throw new PennylaneException('Provider reference not allowed');
            }

            if (!$facture->numero) {
                $invoice = $this->invoice->update(UpdateInvoiceData::fromIpsum($facture, $this->invoice), $facture->provider_reference);
            }

        } else {
            $invoice = $this->invoice->create(CreateInvoiceData::fromIpsum($facture, $brouillon));

            $facture->update($invoice->toIpsum());
        }

        if ($facture->numero and $facture->is_payee) {
            $this->invoice->markAsPaid($facture->provider_reference);
        } // Pas de posibilité de marquer une facture comme impayé via l'api

        return $invoice ?? null;
    }

    public function finalize(Facture $facture): void
    {
        $invoiceData = $this->invoice->finalize($facture->provider_reference);
        $facture->update($invoiceData->toIpsum());

        if ($facture->numero and $facture->is_payee) {
            $this->invoice->markAsPaid($facture->provider_reference);
        } // Pas de posibilité de marquer une facture comme impayé via l'api
    }

    public function delete(Facture $facture): void
    {
        $this->invoice->delete($facture->provider_reference);
    }


    public function sendToCustomer(Facture $facture, bool|string|array $emails = null): void
    {
        if (app()->isLocal() or app()->hasDebugModeEnabled()) {
            return;
        }

        if (!$facture->client->is_entreprise or $emails) {
            if ($emails === true) {
                $emails = [];
            } else {
                $emails = is_array($emails) ? $emails : [$emails];
            }

            $this->invoice->sendByEmail($facture->provider_reference, $emails);

        } else {
            try {
                $this->invoice->sendToPA($facture->provider_reference);
            } catch (\Exception $exception) {
                if ($exception->getCode() === 422) {
                    $this->sendToCustomer($facture, true);
                }
            }
        }

        $facture->send_at = now();
        $facture->save();
    }

    public function getUrlPdf(Facture $facture, $cache = true): string
    {
        if (!$facture->provider_reference) {
            throw new PennylaneException('Réfèrence Pennylane non renseignée.');
        }

        if (!$cache) {
            return $this->invoice->get($facture->provider_reference)->public_file_url;
        }

        return Cache::remember('facture-'.$facture->numero, 5 * 60, function () use ($facture) {
            return $this->invoice->get($facture->provider_reference)->public_file_url;
        });
    }
}


