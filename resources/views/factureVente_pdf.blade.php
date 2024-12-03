@extends("layouts.master")
@section("contenu")
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 1px;
    }
    h1, h2, h3 {
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #333;
        padding: px;
    }
    .total {
        font-weight: bold;
        color: #f00;
    }
    /* Styles spécifiques pour votre mise en page */
    .container {
        margin: 0 auto;
        max-width: 800px; /* Largeur maximale du contenu */
    }
    .facture-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .facture-info {
        margin-bottom: 10px;
    }
    .table-container {
        overflow-x: auto; /* Défilement horizontal pour le tableau */
    }
</style>
<div class="container" id="teste">
    <div class="facture-header">
        <h2>FACTURE VENTE N°{{$id}}</h2>
    </div>
    <div class="facture-info">
        <p><b>Date:</b> {{\Carbon\Carbon::parse($facture->dateVente)->format('d-m-Y')}}</p>
        <p><b>Fournisseur:</b> {{$facture->nomClient}}</p>
        <p><b>Contact:</b> {{$facture->contactClient}}</p>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Designation</th>
                    <th scope="col">P.U</th>
                    <th scope="col">Qt</th>
                    <th scope="col">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articlesFacture as $article)  
                <tr>
                    <td>{{$article->article->designation}}</td>
                    <td>{{ $article->prixUnitaire }}</td>
                    <td>{{ $article->quantite }}</td>
                    <td>{{ $article->quantite * $article->prixUnitaire }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="facture-total">
        <h3>Total: {{$sommeTotal}} Ar</h3>
    </div>
</div>
@endsection
