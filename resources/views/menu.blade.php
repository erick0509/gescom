@extends("layouts.header")
@section("contenuPrincipale")
@php
    $depot = \App\Models\Depot::where('intitule', session('depotValue'))->first();
@endphp

<section  id="article-liste" class="article py-4 ">
    <div class="container">    
        <div class="row mt-5">
            <div class="col-sm-4 mt-2 ">
              <div class="card text-center  text-white bg-success">
                <div class="card-body">
                  <h5 class="card-title">Articles</h5>
                  <a href="{{ route('creationArticle') }}" class="card-link">Création Article</a>
                  <a class="card-link" href="#" data-bs-toggle="modal" data-bs-target="#modalCodeAcces1">Articles dans ce Dépôt</a>
                </div>
              </div>
            </div>
        
            <div class="col-sm-4 mt-2 ">
              <div class="card text-center  text-white bg-primary">
                <div class="card-body">
                  <h5 class="card-title">Transferts</h5>
                  <a class="card-link" href="{{ route('listeTransfert') }}">Documents de Transfert 
                            @if($transfertsAttenteCount > 0)
                                <span class="badge bg-warning text-dark">{{ $transfertsAttenteCount }}</span>
                            @endif
                            </a> 
                  <a class="card-link" href="{{ route('creationTransfert') }}">Création Transfert</a>
                </div>
              </div>
            </div>
            @if($depot && $depot->principal == 1)
              <div class="col-sm-4 mt-2">
                <div class="card text-center  text-dark bg-warning">
                  <div class="card-body">
                    <h5 class="card-title">Achats</h5>
                    <a href="{{ route('documentachat') }}" class="card-link text-dark">Documents des Achats</a>
                    <a href="{{ route('factureAchat') }}" class="card-link text-dark">Facture d'achat</a>
                  </div>
                </div>
              </div>
            @endif
            <div class="col-sm-4 mt-2" >
                <div class="card text-center  text-white bg-danger">
                  <div class="card-body">
                    <h5 class="card-title">Ventes</h5>
                    <a href="{{ route('documentvente') }}" class="card-link">Documents de ventes</a>
                    <a href="{{route('factureVente')}}" class="card-link">Commande client</a>
                    <a href="{{route('caisse')}}" class="card-link">Vente a confirmer
                        @if($facturesVenteAttenteCount > 0)
                                        <span class="badge bg-warning text-dark">{{ $facturesVenteAttenteCount }}</span>
                                      @endif
                    </a>
                  </div>
                </div>
              </div>
        </div>
    </div>
    <div class="modal fade" id="modalCodeAcces1" tabindex="-1" aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <label for="codeAccesInput" class="form-label">Entrez le code d'accès :</label>
                <input type="password" class="form-control" id="codeAccesInput1">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button id="validerCodeAcces1" type="button" class="btn btn-primary">Valider</button>
              </div>
            </div>
          </div>
        </div>
</section>
<script>
          document.getElementById('validerCodeAcces1').addEventListener('click', function() {
                // Récupérer le code d'accès
                let codeAcces = document.getElementById('codeAccesInput1').value;

                // Appeler le serveur pour vérifier le code d'accès
                fetch('/check-code-acces', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ajoute le token CSRF pour la requête
                    },
                    body: JSON.stringify({ code_acces: codeAcces })
                })
                .then(response => response.json())
                .then(data => {
                    // Rediriger vers la route 'articleParDepot' avec le paramètre de validité du code
                    let codeValide = data.success ? 1 : 0; // 1 si le code est correct, 0 sinon
                    window.location.href = `{{ route('articleParDepot') }}?codeValide=${codeValide}`;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            });

  </script>
@endsection