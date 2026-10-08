<?php

namespace PixellWeb\Pennylane\app\Console\Commands;



use Illuminate\Console\Command;
use Illuminate\Container\EntryNotFoundException;
use Ipsum\Reservation\app\Enum\FactureEtat;
use Ipsum\Reservation\app\Models\Reservation\Facture;
use PixellWeb\Pennylane\app\FacturePennylaneProvider;


class SendToCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pennylane:send-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoi Pennylane des factures au client';

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
     * @return int
     * @throws EntryNotFoundException
     */
    public function handle(FacturePennylaneProvider $facturePennylaneProvider): int
    {
        $factures = Facture::whereNull('send_at')
            ->whereNotNull('numero')
            ->where('etat', FactureEtat::VALIDEE)
            ->where('emission_at', '>', now()->subDays(10)) // Permet de ne pas envoyer de trop vielles facture
            ->get();

        $factures->each(function (Facture $facture) use ($facturePennylaneProvider) {
            $this->info('Envoi facture '.$facture->numero);
            try {
                $facturePennylaneProvider->sendToCustomer($facture);
            } catch (\Exception $exception) {
                $this->alert($exception->getMessage());
            }
        });


        return self::SUCCESS;

    }





}
