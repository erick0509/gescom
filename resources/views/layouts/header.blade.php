@extends("layouts.master")
@section("contenu")
<style>
  .cc-navbar {
    padding: 0.5rem 1rem; /* Réduire le padding global */
  }

  .cc-navbar .navbar-brand {
    font-size: 1.25rem; /* Taille de la marque du dépôt */
    margin-right: 1rem; /* Ajustement des marges */
  }

  .cc-navbar .nav-link {
    font-size: 0.9rem; /* Taille des liens de navigation */
    padding: 0.25rem 0.75rem; /* Réduire les paddings des liens */
  }

  .cc-navbar .dropdown-menu {
    font-size: 0.85rem; /* Réduire la taille des éléments du dropdown */
  }

  .cc-navbar .btn-sm {
    padding: 0.25rem 0.75rem; /* Réduire la taille du bouton de déconnexion */
  }

  .cc-navbar .navbar-toggler {
    padding: 0.25rem 0.5rem; /* Taille plus petite du bouton burger */
  }
</style>
<style>
  .cc-navbar {
    background-color: black; /* Fond noir */
    padding: 0.5rem 1rem;
  }

  .cc-navbar .navbar-brand, 
  .cc-navbar .nav-link, 
  .cc-navbar .dropdown-item, 
  .cc-navbar .btn-connecter {
    color: white !important; /* Texte en blanc */
  }

  .cc-navbar .dropdown-menu {
    background-color: #333; /* Fond du dropdown légèrement plus clair */
  }

  .cc-navbar .dropdown-menu .dropdown-item {
    color: white !important; /* Texte blanc dans le dropdown */
  }

  .cc-navbar .dropdown-menu .dropdown-item:hover {
    background-color: #555; /* Couleur de survol pour les éléments du dropdown */
  }

  .cc-navbar .btn-connecter {
    background-color: #f8c94d; /* Garder le bouton jaune de déconnexion */
    color: black !important; /* Texte noir pour le bouton */
  }

  .navbar-toggler {
    border-color: white; /* Couleur blanche pour la bordure du bouton burger */
  }

  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='white' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e"); /* Icône burger blanche */
  }
  .cc-navbar .btn-connecter {
    background-color: #6c757d; /* Couleur de fond gris */
    color: white !important; /* Texte blanc */
      border: none; /* Supprimer la bordure */
  }

  .cc-navbar .btn-connecter:hover {
      background-color: #5a6268; /* Gris foncé au survol */
  }
</style>
@php
    $depot = \App\Models\Depot::where('intitule', session('depotValue'))->first();
@endphp

<header>
        <nav class="cc-navbar navbar position-fixed navbar-expand-lg navbar-light w-100 ">
            <div class="container-fluid ">
                <h3 class="navbar-brand text-dark text-uppercase mx-4 py-2" href="#">{{ session('depotValue') }}</h3>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                      <li class="nav-item pe-4 dropdown">
                          <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownArticles" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-box me-2"></i>Articles
                              @if($transfertsAttenteCount > 0)
                                  <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-warning text-dark">
                                      <i class="fas fa-exclamation-circle"></i>
                                  </span>
                              @endif
                          </a>
                          <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdownArticles">
                            @if($depot && $depot->principal == 1)
                              <li><a class="dropdown-item" href="{{ route('creationArticle') }}">Création Article</a></li>
                            @endif
                              <!-- <li><a class="dropdown-item" href="{{ route('articleParDepot') }}">Articles dans ce Dépôt</a></li> 
                              Articles dans ce Dépôt</a>
                              </li> -->
                              <li>
                                  <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalCodeAcces12">Articles dans ce Dépôt</a>
                              </li>

                              <li><hr class="dropdown-divider"></li>
                              <li>
                                <a class="dropdown-item" href="{{ route('listeTransfert') }}">
                                    Documents de Transfert
                                      @if($transfertsAttenteCount > 0)
                                        <span class="badge bg-warning text-dark">{{ $transfertsAttenteCount }}</span>
                                      @endif
                                </a>
                              </li>
                              <li><a class="dropdown-item" href="{{ route('creationTransfert') }}">Création Transfert</a></li>
                          </ul>
                      </li>
                      @if($depot && $depot->principal == 1)
                      <li class="nav-item pe-4 dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-shopping-cart me-2"></i>Achats
                        </a>
                        <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">                            
                           <li><a class="dropdown-item" href="{{ route('documentachat') }}">Documents des achats</a></li>
                           <li><a class="dropdown-item" href="{{ route('factureAchat') }}">Facture d'achat</a></li>
                        </ul>
                      </li>
                      @endif
                      <li class="nav-item pe-4 dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-receipt me-2"></i>Ventes
                          @if($facturesVenteAttenteCount > 0)
                              <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-warning text-dark "> <!-- Utilisation de 'me-1' pour l'espacement -->
                                  <i class="fas fa-exclamation-circle"></i>
                              </span>
                          @endif
                          @if($facturesVenteEcheanceCount > 0)
                              <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger text-dark "> <!-- Utilisation de 'me-1' pour l'espacement -->
                                  <i class="fas fa-exclamation-circle"></i>
                              </span>
                          @endif 
                        </a>
                        <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">                            
                           <li><a class="dropdown-item" href="{{route('factureVente')}}">Commande client</a></li>
                           <li><a class="dropdown-item" href="{{ route('documentvente') }}">Document de vente</a></li>
                           <li><a class="dropdown-item" href="{{route('caisse')}}">Ventes a confirmer 
                                  @if($facturesVenteAttenteCount > 0)
                                        <span class="badge bg-warning text-dark">{{ $facturesVenteAttenteCount }}</span>
                                      @endif
                           </a></li>
                           <li>
                              <a class="dropdown-item" href="{{ route('documentvente', ['echeance' => 'echue']) }}">
                                  Factures en échéance 
                                  @if($facturesVenteEcheanceCount > 0)
                                      <span class="badge bg-danger text-dark">{{ $facturesVenteEcheanceCount }}</span>
                                  @endif
                              </a>
                          </li>
                        </ul>
                      </li>
                      <li class="nav-item pe-4 dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-address-book me-2"></i>Compte Tiers
                        </a>
                        <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">                            
                           <li><a class="dropdown-item" href="{{route('clients.index')}}">Clients</a></li>
                           <li><a class="dropdown-item" href="{{route('fournisseurs.index')}}">Fournisseurs</a></li>
                        </ul>
                      </li>
                      <li class="nav-item pe-4 dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-cash-register me-2"></i>Caisse
                        </a>
                        <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">                            
                           <li><a class="dropdown-item" href="{{ route('debitCaisse') }}">Debiter /Crediter la Caisse</a></li>
                           <li><a class="dropdown-item" href="{{route('clients.index')}}">Avancement Client</a></li>
                        </ul>
                      </li>
                      <li class="nav-item pe-4">
                        <a class=" nav-link text-dark text-truncate"  href="{{route('accueil')}}"><i class="fas fa-store-alt me-2"></i>Magasins</a>
                      </li>
                      <li class="nav-item pe-4 dropdown">
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-cog me-2"></i>Parametre
                        </a>
                        <ul class="dropdown-menu rounded-0" aria-labelledby="navbarDropdown">                            
                           <li><form id="parametre" action="{{ route('auth.parametre') }}" method="get">
                                @csrf
                              </form>
                              <a class="dropdown-item"   href="#"onclick="allerVersParametre()"><i class="fas fa-cog me-2"></i>Authentification Systeme</a></li>
                           <li><form id="parametreCode" action="{{ route('code-acces-depot.parametre') }}" method="get">
                                  @csrf
                                </form>
                                <a class="dropdown-item"   href="#" onclick="allerVersParametreCode()"><i class="fas fa-lock me-2"></i>Code d'acces du Depot</a></li>
                           
                        </ul>
                      </li>
                      
                      <li class="nav-item pe-4 mt-1">
                        <form id="logout-form" action="{{ route('auth.logout') }}" method="post">
                           
                            @csrf
                            <button type="submit" class="btn btn-link btn-connecter btn-jaune btn-sm">
                                <i class="fas fa-sign-out-alt me-2"></i> Se déconnecter
                            </button>
                        </form>
                      </li>
                    </ul>  
                </div>
            </div>
          </nav>
</header> 
        <div class="modal fade" id="modalCodeAcces" tabindex="-1" aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <label for="codeAccesInput" class="form-label">Entrez le code d'accès :</label>
                <input type="password" class="form-control" id="codeAccesInput">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button id="validerCodeAcces" type="button" class="btn btn-primary">Valider</button>
              </div>
            </div>
          </div>
        </div>

         <div class="modal fade" id="modalCodeAcces12" tabindex="-1" aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <label for="codeAccesInput" class="form-label">Entrez le code d'accès :</label>
                <input type="password" class="form-control" id="codeAccesInput12">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button id="validerCodeAcces12" type="button" class="btn btn-primary">Valider</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="modalCodeAcces2" tabindex="-1" aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <label for="codeAccesInput" class="form-label">Entrez le code d'accès :</label>
                <input type="password" class="form-control" id="codeAccesInput2">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button id="validerCodeAcces2" type="button" class="btn btn-primary">Valider</button>
              </div>
            </div>
          </div>
        </div>
@yield("contenuPrincipale")
<script>
    function allerVersParametre() {
            var modalCodeAcces = new bootstrap.Modal(document.getElementById('modalCodeAcces'));
            modalCodeAcces.show();
            document.getElementById('validerCodeAcces').addEventListener('click', function() {
                var codeAcces = document.getElementById('codeAccesInput').value;
                
                // Créer une requête fetch pour vérifier le code d'accès
                fetch('/check-code-acces', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ code_acces: codeAcces })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur HTTP, status = ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('parametre').submit();
                        modalCodeAcces.hide();
                    } else {
                        alert('Code d\'accès incorrect.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du code d\'accès:', error);
                });
            });
      //
    }
    function allerVersParametreCode() {
            var modalCodeAcces = new bootstrap.Modal(document.getElementById('modalCodeAcces2'));
            modalCodeAcces.show();
            document.getElementById('validerCodeAcces2').addEventListener('click', function() {
                var codeAcces = document.getElementById('codeAccesInput2').value;
                
                // Créer une requête fetch pour vérifier le code d'accès
                fetch('/check-code-acces', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ code_acces: codeAcces })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur HTTP, status = ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('parametreCode').submit();
                        modalCodeAcces.hide();
                    } else {
                        alert('Code d\'accès incorrect.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du code d\'accès:', error);
                });
            });
      //
    }
    
</script>
  <script>
          document.getElementById('validerCodeAcces12').addEventListener('click', function() {
                // Récupérer le code d'accès
                let codeAcces = document.getElementById('codeAccesInput12').value;

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