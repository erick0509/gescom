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
            <div id="print-section-etat-client">
                <div class="border border-secondary rounded p-3 position-relative">
                    <div class="row">
                        <!-- Première colonne -->
                        <div class="col-2 col-md-2">
                            <div class="position-absolute top-0 start-0 m-2">
                                <p class="text-muted fs-6"><b>{{ session('depotValue') }}</b></p>
                            </div>
                        </div>
                        <!-- Deuxième colonne -->
                        <div class="col-md-8">
                            @if($etatText==='payee')
                                <h1 class="mb-0 mt-3">SOLDE PAYEE PAR CLIENT</h1>
                            @else
                                <h1 class="mb-0 mt-3">SOLDE DU PAR CLIENT</h1>
                            @endif
                        </div>
                        <!-- Troisième colonne -->
                        <div class="position-absolute top-0 end-0 col-2 col-md-2">    
                            <p class="text-muted fs-6">periode au <b>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}  {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</b></p>
                        </div>
                    </div>
                </div> 
                        @php
                            $totalSommePayee = 0;
                            $totalSommeDu=0;
                            $totalsommeTotaleVentes = 0;
                        @endphp
                <div class="row">
                    <div class="col text-start">
                        <p class=" fs-6"  style="color:black;">Date de tirage {{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Date Echeance</th>
                            <th scope="col">N° Facture</th>
                            <th scope="col">Client</th>
                            <th scope="col">Contact</th>
                            @if($etatText==='payee')
                                <th scope="col">Solde Payee</th>
                            @else
                                <th scope="col">Total</th>
                                <th scope="col">Montant Payee</th>
                                <th scope="col">Solde Du</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factures as $facture)
                        <tr>
                            <td>{{\Carbon\Carbon::parse($facture->dateVente)->format('d/m/Y')}}</td>
                            <td>{{\Carbon\Carbon::parse($facture->dateEcheance)->format('d/m/Y')}}</td>
                            <td>{{$facture->primaryKey}}</td>
                            <td>{{$facture->client->intituleClient}}</td>
                            <td>{{$facture->client->contactClient}}</td>
                            @php
                                $sommeTotaleVentes = 0; // Initialisation de la somme totale des ventes
                            @endphp
                            @foreach($facture->articlesVente as $articleVente)
                                @php
                                    $sommeTotaleVentes += $articleVente->quantite * $articleVente->prixUnitaire; // Calcul de la somme totale des ventes pour chaque article
                                @endphp
                            @endforeach
                            @if($etatText === 'payee')
                                <td class="no-wrap">{{ number_format($facture->sommePayee, 2, ',', ' ') }}</td>
                            @else
                                <td class="no-wrap">{{ number_format($sommeTotaleVentes-$facture->remise, 2, ',', ' ') }}</td>
                                <td class="no-wrap">{{ number_format($facture->sommePayee, 2, ',', ' ') }}</td>
                                <td class="no-wrap">{{ number_format(($sommeTotaleVentes-$facture->remise)-$facture->sommePayee, 2, ',', ' ') }}</td>
                            @endif
                        </tr>
                                    @php
                                        $totalSommePayee += $facture->sommePayee;
                                        $totalSommeDu += (($sommeTotaleVentes-$facture->remise)-$facture->sommePayee);
                                        $totalsommeTotaleVentes += $sommeTotaleVentes-$facture->remise;
                                    @endphp
                                @endforeach                              
                                <!-- Ligne pour afficher les totaux -->
                        <tr>
                            <td><b>TOTAL</b></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            @if($etatText === 'payee')
                                <td class="no-wrap"><b>
                                @if($codeAccesValide === true)
                                    {{ number_format($totalSommePayee, 2, ',', ' ') }}
                                @endif</b></td>
                            @else
                                <td class="no-wrap"><b>
                                @if($codeAccesValide === true)
                                    {{ number_format($totalsommeTotaleVentes, 2, ',', ' ') }}
                                @endif</b></td>
                                <td class="no-wrap"><b>
                                @if($codeAccesValide === true)
                                    {{ number_format($totalSommePayee, 2, ',', ' ') }}
                                @endif</b></td>
                                <td class="no-wrap"><b>
                                @if($codeAccesValide === true)
                                    {{ number_format($totalSommeDu, 2, ',', ' ') }}
                                @endif</b></td>
                            @endif
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