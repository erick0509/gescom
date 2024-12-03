@extends("layouts.master")
@section("contenu")
<style>
    .no-wrap {
        white-space: nowrap; /* Empêche le contenu de se mettre à la ligne */
    }
</style>
<div id="container">
    <div class="row d-flex text-center justify-content-center align-items-center ">
        <div class="col-md-8 col-8">
            <div id="print-section-etat">
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
                            <h1 class="mb-0 mt-3">ETAT DE VENTES PAR ARTICLES</h1>
                        </div>
                        <!-- Troisième colonne -->
                        <div class="position-absolute top-0 end-0 col-2 col-md-2">    
                            <p class="text-muted fs-6">periode au <b>{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}  {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</b></p>
                        </div>
                    </div>
                </div> 
                        @php
                            $totalCaNetHt = 0;
                            $totalCaAchatNetHt=0;
                            $totalGainPerte = 0;
                            $totalQtVendu = 0;
                            $totalPV = 0;
                            $totalPA = 0;
                        @endphp
                <div class="row">
                    <div class="col text-start">
                        <p class=" fs-6"  style="color:black;">Date de tirage {{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Designation</th>
                            <th scope="col">Qt Vendu</th>
                            <th scope="col">Stock Restant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articlesVendus as $articleVendu)
                        <tr>
                            
                            <td>{{$articleVendu->designation}}</td>
                            
                            <td>{{ $articleVendu->totalQuantite }}</td>

                            <td>{{ $articleVendu->quantiteRestante}}</td>
                        </tr>
                                    @php
                                        
                                        $totalQtVendu +=$articleVendu->totalQuantite;
                                        
                                    @endphp
                                @endforeach
                                <!-- Ligne pour afficher les totaux -->
                        
                        <tr>
                            <td><b>TOTAL</b></td>
                            <td ><b>{{ $totalQtVendu}}</b></td>
                            
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