@extends("layouts.master")
@section("contenu")
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
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
        padding: 8px;
    }
    .total {
        font-weight: bold;
        color: #f00;
    }
</style>

<div class="container"  style="max-width: 100%;"> 
      <div class="row mt-1" >
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-4 border-bottom">
            </div> 
            
            <div class="row d-flex text-center justify-content-center align-items-center ">
              <div class="col-md-6">
              <div class="d-flex justify-content-start">
                  <h1 class="h2">FACTURE ACHAT N°{{$id}}</h1>
              </div>
              <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
              </div> 
              <div class="d-flex justify-content-start">
                  <p><b>date:</b> {{\Carbon\Carbon::parse($facture->dateAchat)->format('d-m-Y')}}</p>
              </div>
              <div class="d-flex justify-content-start">
                  <p><b>reference:</b> {{$facture->ReferenceFactureAchat}}</p>
              </div>
              <div class="d-flex justify-content-start">
                  <p><b>fournisseur:</b> {{$facture->nomFournisseur}}</p>
              </div>
              <div class="d-flex justify-content-start">
                  <p><b>contact:</b> {{$facture->contactFournisseur}}</p>
              </div>
              <table class="table ">
                <thead>
                  <tr>
                    <th scope="col">Designation</th>
                    <th scope="col">P.Achat</th>
                    <th scope="col">Quantite</th>
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
              <div class="d-flex justify-content-start">
                  <h3 class="me-3">Total: {{$sommeTotal}} Ar</h3>
              </div>
              <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
              </div>
              
              </div>
            </div>            
          </div>
</div>
@endsection