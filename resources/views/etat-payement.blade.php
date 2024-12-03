@extends("layouts.master")
@section("contenu")
<style>
    .no-wrap {
        white-space: nowrap; /* Empêche le contenu de se mettre à la ligne */
    }
</style>
<div id="container">
    <div class="row d-flex text-center justify-content-center align-items-center ">
        <div class="col-md-8">
            <div id="print-section-etat-payement">
                <div class="border col-12 border-secondary rounded p-3 position-relative">
                    <div class="row">
                        <!-- Première colonne -->
                        <div class="col-2 col-md-2">
                            <div class="position-absolute top-0 start-0 m-2">
                                <p class="text-muted fs-6"><b>{{ session('depotValue') }}</b></p>
                            </div>
                        </div>
                        <!-- Deuxième colonne -->
                        <div class="col-8 col-md-8">
                            <h1 class="mb-0 mt-3">HISTORIQUES DE PAIEMENTS</h1>
                        </div>
                        <!-- Troisième colonne -->
                        <div class="position-absolute top-0 end-0 col-2 col-md-2">    
                            <p class="text-muted fs-6">periode au <b>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}  {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</b></p>
                        </div>
                    </div>
                </div> 
                        @php
                            $totalPayement = 0;
                        @endphp
                <div class="row">
                    <div class="col text-start">
                        <p class=" fs-6"  style="color:black;">Date de tirage {{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
                <table class="table" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th scope="col">Date Document</th>
                            <th scope="col">N° Facture</th>
                            <th scope="col">Client</th>
                            <th scope="col">Contact</th>
                            <th scope="col">Date Paiement</th>
                            <th scope="col">Mode Paiement</th>
                            <th scope="col">Référence</th>
                            <th scope="col">Montant facture</th>
                            <th scope="col">Montant Payée</th>
                            <th scope="col">Solde Du</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payements as $payement)
                        <tr>
                        <td>{{ \Carbon\Carbon::parse($payement->created_at)->format('d/m/y')  }}</td>
                        <td>{{ isset($payement->facture) ? $payement->facture->primaryKey : '-' }}</td>
                        <td>{{ isset($payement->facture) ? $payement->facture->client->intituleClient : $payement->client->intituleClient  }}</td>
                        <td>{{ isset($payement->facture) ? $payement->facture->client->contactClient : $payement->client->intituleClient  }}</td>
                        <td>{{ \Carbon\Carbon::parse($payement->datePayement)->format('d/m/y') }}</td>
                        <td>{{ $payement->mode }}</td>
                        <td>{{ $payement->reference }}</td>
                        @php
                            $sommeTotaleVentes = 0; // Initialisation de la somme totale des ventes
                        @endphp

                        @if (isset($payement->facture) && isset($payement->facture->articlesVente))
                            @foreach($payement->facture->articlesVente as $articleVente)
                                @php
                                    $sommeTotaleVentes += $articleVente->quantite * $articleVente->prixUnitaire; // Calcul de la somme totale des ventes pour chaque article
                                @endphp
                            @endforeach
                            
                        @endif

                        <td class="no-wrap"><b>
                        @if($codeAccesValide === true)
                            {{ isset($payement->facture) ? number_format($sommeTotaleVentes-$payement->facture->remise, 2, ',', ' ') : '-'}}
                        @endif
                        </b></td>
                        <td class="no-wrap"><b>
                        @if($codeAccesValide === true)
                            {{ number_format($payement->somme, 2, ',', ' ') }}
                        @endif</b></td>
                        <td class="no-wrap"><b>
                        @if($codeAccesValide === true)
                            {{ isset($payement->facture) ? number_format($payement->reste, 2, ',', ' ') :'-'}}
                        @endif</b></td>

                        </tr>
                                    @php
                                        $totalPayement+=$payement->somme;
                                    @endphp
                                @endforeach
                                <!-- Ligne pour afficher les totaux -->
                        <tr>
                            <td><b>TOTAL</b></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td ></td>
                            <td class="no-wrap"><b>
                                @if($codeAccesValide === true)
                                    {{number_format($totalPayement, 2, ',', ' ') }}
                                @endif
                                </b></td>
                            <td ></td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
                </div>
            </div>
                <div class="row">
                    <div class="col-md-2 col-4 mt-2">
                        <button onclick="window.print()" class="btn btn-primary btn-sm col-12 mb-1"><i class="fas fa-print"></i></button>
                    </div>
                    <div class="col-md-2 col-4 mt-2">
                        <a class="btn btn-secondary btn-sm col-12 mb-1" href="{{ route('documentvente')}}"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>   
        </div>
    </div>  
</div>
<script>
    function imprimer(elementId) {
        var printContent = document.getElementById(elementId).innerHTML;

        // Styles spécifiques pour l'impression
        var styles = `
            <style>
                /* Styles spécifiques pour l'impression */
                .no-wrap {
                    white-space: nowrap; /* Empêche le contenu de se mettre à la ligne */
                }

                /* Ajoutez d'autres styles d'impression ici */
            </style>
        `;

        var printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.open();
        printWindow.document.write('<html><head><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">' + styles + '</head><body>');
        printWindow.document.write(printContent);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        //printWindow.print();
    }

</script>
@endsection