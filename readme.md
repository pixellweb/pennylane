## Notes

## Mise en place

1) Paramètres > Connectivité > Développeurs ; Générer le token avec droit en lecture écriture sur : Client, Factures client, Produit
2) Renseigner dans le fichier .env

        FACTURE_PROVIDER=pennylane
        PENNYLANE_TOKEN=xxxxxxxx

3) Lancer la commande pour envoyer les produits dans Pennylane

        php artisan pennylane:produit --export 

4) Ajouter la commande dans le Kernel.

        $schedule->command('pennylane:send-invoice')->daily();
5) Configurer, si besoin, la numérotation des factures dans Pennylane





