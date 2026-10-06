<iframe id="facture-iframe" src="" style="width:100%; height: 700px;" frameborder="0"></iframe>

<script>
    /* Délai pour attendre la génération de la facture */
    setTimeout(function() {
        document.getElementById("facture-iframe").src="{{ $urlPdf }}";
    }, 60 * 20);
</script>