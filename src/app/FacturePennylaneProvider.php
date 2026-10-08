<?php

namespace PixellWeb\Pennylane\app;


use Illuminate\View\View;
use Ipsum\Reservation\app\Contracts\FactureContract;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use PixellWeb\Pennylane\app\Actions\IpsumCustomerAction;
use PixellWeb\Pennylane\app\Actions\IpsumInvoiceAction;

class FacturePennylaneProvider implements FactureContract
{

    public function __construct(
        private IpsumInvoiceAction $invoiceAction,
        private IpsumCustomerAction $customerAction,
    ) {}

    public function syncToProvider(Facture $facture, bool $brouillon = true): void
    {
        $this->customerAction->syncToProvider($facture);
        $this->invoiceAction->syncToProvider($facture, $brouillon);
    }

    public function emmission(Facture $facture): void
    {
        $this->invoiceAction->finalize($facture);
    }

    public function sendToCustomer(Facture $facture, bool|array|string $emails = null): void
    {
        $this->invoiceAction->sendToCustomer($facture, $emails);
    }

    public function delete(Facture $facture): void
    {
        $this->invoiceAction->delete($facture);
    }

    public function getUrlPdf(Facture $facture, $cache = true): string
    {
        return $this->invoiceAction->getUrlPdf($facture, $cache);
    }

    public function iframe(): View
    {
        return view('Pennylane::iframe');
    }
}