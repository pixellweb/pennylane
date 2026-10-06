<?php

namespace PixellWeb\Pennylane\app\Ressources;



use PixellWeb\Pennylane\app\Data\Requests\CreateInvoiceData;
use PixellWeb\Pennylane\app\Data\Requests\InvoiceLinesData;
use PixellWeb\Pennylane\app\Data\Requests\UpdateInvoiceData;
use PixellWeb\Pennylane\app\Data\Responses\InvoiceData;

class Invoice extends Ressource
{

    public function list(): \Spatie\LaravelData\CursorPaginatedDataCollection|\Spatie\LaravelData\DataCollection|\Spatie\LaravelData\PaginatedDataCollection
    {
        $invoices = $this->crawler->get('customer_invoices');

        return InvoiceData::collection($invoices['items']);
    }

    public function get(int $id): InvoiceData
    {
        $invoice = $this->crawler->get('customer_invoices/'.$id);

        return InvoiceData::from($invoice);
    }

    public function create(CreateInvoiceData $invoice_data): InvoiceData
    {
        $invoice = $this->crawler->post('customer_invoices', $invoice_data->toArray());

        return InvoiceData::from($invoice);
    }

    public function update(UpdateInvoiceData|array $invoice_data, int $invoice_id): InvoiceData
    {
        $data = is_object($invoice_data) ? $invoice_data->toArray() : $invoice_data;

        $invoice = $this->crawler->put('customer_invoices/'.$invoice_id, $data);

        return InvoiceData::from($invoice);
    }

    public function delete(int $id): void
    {
        $this->crawler->delete('customer_invoices/'.$id);
    }

    public function sendByEmail(int $id, array $emails = [])
    {
        return $this->crawler->post('customer_invoices/'.$id.'/send_by_email', ['recipients' => $emails]);
    }

    /*
     * Envoi E-facture
     */
    public function sendToPA(int $id)
    {
        return $this->crawler->post('customer_invoices/'.$id.'/send_to_pa');
    }

    public function finalize(int $id): InvoiceData
    {
        $invoice = $this->crawler->put('customer_invoices/'.$id.'/finalize');

        return InvoiceData::from($invoice);
    }


    public function listLines(int $id): array
    {
        $invoices = $this->crawler->get('customer_invoices/'.$id.'/invoice_lines', ['limit' => 100]);

        return $invoices['items'];
    }
}