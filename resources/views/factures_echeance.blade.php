@extends("layouts.header")
@section("contenuPrincipale")
<style>
    /* CSS pour centrer le modal */
    #modaleEtatVente ,#modaleEtatClient,#modalePayement{
        display: none; /* Cache le modal par défaut */
        position: fixed; /* Position fixe pour le modal */
        z-index: 1; /* Assure que le modal est au-dessus de tout le contenu */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto; /* Permet le défilement si le contenu du modal est plus grand que l'écran */
        background-color: rgba(0,0,0,0.4); /* Fond semi-transparent pour le modal */
    }

    /* Style du contenu du modal */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% du haut et centré horizontalement */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Largeur du modal */
        max-width: 500px; /* Largeur maximale du modal */
    }

    /* Style pour le bouton de fermeture (×) */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .no-wrap {
        white-space: nowrap; /* Empêche le contenu de se mettre à la ligne */
    }

</style>
<section  id="article-liste" class="article py-4 ">
  <div class="container">    
        <div class="row mt-5">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2>Documents des Ventes</h2>
          </div>    
        </div>
          @if(session()->has("success"))
                <div class="alert alert-success">
                    {{session()->get('success')}}
                </div>
            @endif

            @if(session()->has("successDelete"))
                <div class="alert alert-success">
                    {{session()->get('successDelete')}}
                </div>
            @endif

            @if($errors->any())
                <ul class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            @endif
        <h1 class="h2">Liste des Factures en Echeance <span class="badge bg-secondary">{{ $totalFacture}}</span></h1>
        <div class="row d-flex text-center justify-content-center align-items-center">
            <a class="btn btn-primary btn-sm col-md-4 col-sm-6 mb-1" type="submit" href="{{ route('factureVente') }}">Nouvelle commande</a>
        </div>
        <div class="row d-flex text-center justify-content-center align-items-center">
          <form id="formDate" action="{{ route('rechercherParDate.documentvente') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
            @csrf
            <div class="input-group">
              <input value="{{ $zoneChercherDate ?? date('Y-m-d') }}" id="zoneChercherDate" name="zoneChercherDate" class="form-control" type="date" placeholder="Recherche par date..." aria-label="Search">
            </div>
          </form>
          <form action="{{ route('rechercher.documentvente') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
          @csrf
            <div class="input-group ">
              <div class="input-group-append">
                <select class="form-select" aria-label="Filtrer par" name="filterBy" id="filterBy">
                  <option value="id"  @if(isset($filterBy) && $filterBy == 'id') selected @endif>Id</option>
                  <option value="client" @if(isset($filterBy) && $filterBy == 'client') selected @endif>Client</option>
                </select>
              </div>
              <input value="{{ $zoneChercher ?? old('zoneChercher') }}" id="zoneChercher" name="zoneChercher" class="form-control" type="search" placeholder="Recherche..." aria-label="Search">
              <button id="btnSearch" class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </form>
          
          <form id="formStatut" action="{{ route('rechercherParStatut.documentvente') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
            @csrf
            <div class="input-group">
              <select class="form-select" aria-label="Filtrer par" name="filterByStatut" id="filterByStatut">
                <option value="tout"  @if(isset($filterByStatut) && $filterByStatut == 'tout') selected @endif>Tout</option>
                <option value="en attente" @if(isset($filterByStatut) && $filterByStatut == 'en attente') selected @endif>En attente</option>
                <option value="non payee" @if(isset($filterByStatut) && $filterByStatut == 'non payee') selected @endif>Non payee</option>
                <option value="payee" @if(isset($filterByStatut) && $filterByStatut == 'payee') selected @endif>Payee</option>
              </select>
            </div>
          </form>
        </div>
        @if($ventesDepot->isEmpty()&& $ventesDepot->currentPage() === 1)
            <div class="alert alert-danger text-center mt-2" role="alert">
                <p>Les documents des factures de ventes est vide!</p>.
            </div>
        @else
          <div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">
          <table class="table table-striped table-hover mt-3 ">
            <thead class="table-light">
              <tr>
                <th scope="col">N° Pièce</th>
                <th scope="col">Date Pièce</th>
                <th scope="col">Date Echeance</th>
                <th scope="col">Client</th>
                <th scope="col">Contact</th>
                <th scope="col">Total TTC</th>
                <th scope="col">Solde Dû</th>
                <th scope="col" class="texte-court">Actions</th>
              </tr>
            </thead>
            <tbody>
                @foreach($ventesDepot as $facture)
                <tr style="font-size: 13px;">
                  <td><b> {{$facture->primaryKey}}</b>
                    @if($facture->statut == 'en attente')
                                    <!-- Icône pour statut en attente -->
                                    <i class="fas fa-hourglass-start" title="Facture en attente" style="color: orange;"></i>
                                @else
                                    <!-- Autre contenu ou icône pour d'autres statuts -->
                                    <i class="fas fa-check-circle" title="Facture terminé" style="color: green;"></i>
                                    @if(\Carbon\Carbon::parse($facture->dateEcheance)->isPast() && $facture->statut == 'non payee')
                                        <i class="fas fa-exclamation-triangle" title="Facture échue" style="color: red; margin-left: 5px;"></i>
                                    @endif
                                @endif</b></td></td>
                  <td style="text-align: center;"><b>{{\Carbon\Carbon::parse($facture->created_at)->format('d/m/Y')}}</b></td>
                  <td style="text-align: center;"><b>{{\Carbon\Carbon::parse($facture->dateEcheance)->format('d/m/Y')}}</b></td>
                  <td style="text-align: center;"><b>{{$facture->client->intituleClient}}</b></td>
                  <td style="text-align: center;"><b>{{$facture->client->contactClient}}</b></td>
                  <td style="text-align: right;" class="no-wrap"><b>{{number_format($facture->montantTotal,1,',',' ')}}</b></td>
                  <td style="text-align: right;" class="no-wrap"><b>{{number_format($facture->montantTotal-$facture->sommePayee,1,',',' ')}}</b></td>
                  <td class="texte-court">
                    <div class="d-flex justify-content-center">
                      <form id="form-{{$facture->id}}" action="{{ route('documentvente.details',['id' => $facture->id, 'page' => $ventesDepot->currentPage()])}}" method="get">
                        @csrf
                        <button class="btn-details-facture-achats btn btn-warning btn-sm" ><i class="fas fa-info-circle"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
                @endforeach
            </tbody>
          </table>
          </div>
            <div class="row mt-2 d-flex text-center justify-content-center align-items-center">
              <b>{{$ventesDepot->currentPage()}}</b>
            </div>
            <div class="row mt-1 d-flex text-center justify-content-start align-items-center">
              {{$ventesDepot->links()}}
            </div>
        @endif   
        <div class="row">
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
            <div class="modal fade" id="modalCodeAcces3" tabindex="-1" aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <label for="codeAccesInput" class="form-label">Entrez le code d'accès :</label>
                    <input type="password" class="form-control" id="codeAccesInput3">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button id="validerCodeAcces3" type="button" class="btn btn-primary">Valider</button>
                  </div>
                </div>
              </div>
            </div>
                <div class="col-6 col-md-4 mt-2">
                    <a onclick="etatVente()" class="btn btn-primary secondary col-12 mb-1"><i class="fas fa-list-ul"></i> Etat de vente</a>  
                </div>
                <div class="col-6 col-md-4 mt-2"> 
                    <a onclick="etatClient()" class="btn btn-primary col-12 mb-1"><i class="fas fa-list-ul"></i> Etat de clients</a>  
                </div>
                <div class="col-6 col-md-4 mt-2"> 
                    <a onclick="etatPayement()" class="btn btn-primary col-12 mb-1"><i class="fas fa-list-ul"></i> Paiements </a>  
                </div>
                
                    <div id="modalePayement" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModale()" class="close">&times;</span>
                            <p>Selectionner une periode:</p>
                            <form id="etatPayement" action="{{ route('etatPayement.documentvente')}}" method="POST">
                                @csrf  
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Debut:</span>
                                    <input type="date" id="dateDebutPayement" name="dateDebutPayement" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Fin:</span>
                                    <input type="date" id="dateFinPayement" name="dateFinPayement" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </div>  
                                <button type="button" onclick="selectionDatePayement()" class="btn btn-primary mb-2 form-control">Generer l'etat</button>                
                            </form>                                           
                            <button onclick="fermerModale()" class="btn btn-danger">Annuler</button>
                        </div>
                    </div>
                    <div id="modaleEtatVente" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModale()" class="close">&times;</span>
                            <p>Selectionner une periode:</p>
                            <form id="etatArticle" action="{{ route('etatArticle.documentvente')}}" method="POST">
                                @csrf  
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Debut:</span>
                                    <input type="date" id="dateDebut" name="dateDebut" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Fin:</span>
                                    <input type="date" id="dateFin" name="dateFin" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </div>  
                                <button type="button" onclick="selectionDate()" class="btn btn-primary mb-2 form-control">Generer l'etat</button>                
                            </form>                                           
                            <button onclick="fermerModale()" class="btn btn-danger">Annuler</button>
                        </div>
                    </div>
                    <div id="modaleEtatClient" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModale()" class="close">&times;</span>
                            <p>Selectionner l'etat du solde et une periode:</p>
                            <form id="etatClient" action="{{ route('etatClient.documentvente')}}" method="POST">
                                @csrf  
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Etat:</span>
                                    <select id="selectEtat" class="form-select" aria-label="ListeArticles" name="etat">
                                        <option value="0" selected>payee</option>
                                        <option value="1">non payee</option>
                                    </select>
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Debut:</span>
                                    <input type="date" id="dateDebutClient" name="dateDebutClient" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon3">Fin:</span>
                                    <input type="date" id="dateFinClient" name="dateFinClient" class="form-control" id="basic-url" aria-describedby="basic-addon3">
                                </div>  
                                <button type="button" onclick="selectionDateClient()" class="btn btn-primary mb-2 form-control">Generer l'etat</button>                
                            </form>                                           
                            <button onclick="fermerModale()" class="btn btn-danger">Annuler</button>
                        </div>
                    </div>
            </div> 
  </div>
</section>
<script src="{{asset('js/jquery-3.6.0.min.js')}}">
</script>
<script>
    $(document).ready(function() {
        $('#zoneChercherDate').on('change', function() {
            $('#formDate').submit();
        });
    });
    $(document).ready(function() {
        $('#filterByStatut').change(function() {
            $('#formStatut').submit(); // Soumettre le formulaire lorsque la valeur change
        });
    });
    
</script>
<script>
    var modal = document.getElementById('modaleEtatVente');
    var modalClient = document.getElementById('modaleEtatClient');
    var modalPayement = document.getElementById('modalePayement');
    var btnSearch = document.getElementById('btnSearch');
    // Fonction pour afficher la fenêtre modale
    
    function etatPayement(){
            var modalCodeAcces = new bootstrap.Modal(document.getElementById('modalCodeAcces1'));
            modalCodeAcces.show();
            document.getElementById('validerCodeAcces1').addEventListener('click', function() {
                var codeAcces = document.getElementById('codeAccesInput1').value;
                
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
                        modalPayement.style.display = 'block';
                        btnSearch.style.display = 'none';
                        modalCodeAcces.hide();
                         
                    } else {
                        alert('Code d\'accès incorrect.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du code d\'accès:', error);
                });
            });
        
    }
    function etatVente() {
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
                        modal.style.display = 'block';
                        btnSearch.style.display = 'none';
                        modalCodeAcces.hide();
                        
                    } else {
                        alert('Code d\'accès incorrect.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du code d\'accès:', error);
                });
            });
        
    }
    function etatClient() {
            var modalCodeAcces = new bootstrap.Modal(document.getElementById('modalCodeAcces3'));
            modalCodeAcces.show();
            document.getElementById('validerCodeAcces3').addEventListener('click', function() {
                var codeAcces = document.getElementById('codeAccesInput3').value;
                
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
                        modalClient.style.display = 'block';
                        btnSearch.style.display = 'none';
                        modalCodeAcces.hide();
                         
                    } else {
                        alert('Code d\'accès incorrect.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du code d\'accès:', error);
                });
            });
       
    }
    function fermerModale() {
        modal.style.display = 'none';
        modalClient.style.display = 'none';
        modalPayement.style.display = 'none';
        btnSearch.style.display = 'block';
    }
    window.onclick = function(event) {
        if (event.target == modal) {
          fermerModale();
        }
        if (event.target == modalClient) {
          fermerModale();
        }
        if (event.target == modalPayement) {
          fermerModale();
        }
    }

    function selectionDate() {
        var dateDebut = document.getElementById('dateDebut').value;
        var dateFin = document.getElementById('dateFin').value;

        if (dateDebut === '' || dateFin === '') {
            alert('Veuillez sélectionner à la fois une date de début et une date de fin.');
            return;
        }

        if (dateDebut > dateFin) {
            alert('La date de début doit être inférieure ou égale à la date de fin.');
            return;
        }
        if (dateDebut <= dateFin) {
            document.getElementById('etatArticle').submit(); // Exemple pour soumettre le formulaire
            return;
        }

        // Si les validations passent, vous pouvez effectuer d'autres actions comme l'envoi du formulaire ou le traitement des dates.
    }
    function selectionDateClient() {
        var dateDebut = document.getElementById('dateDebutClient').value;
        var dateFin = document.getElementById('dateFinClient').value;
        if (dateDebut === '' || dateFin === '') {
            alert('Veuillez sélectionner à la fois une date de début et une date de fin.');
            return;
        }

        if (dateDebut > dateFin) {
            alert('La date de début doit être inférieure ou égale à la date de fin.');
            return;
        }
        if (dateDebut <= dateFin) {
            document.getElementById('etatClient').submit(); // Exemple pour soumettre le formulaire
            return;
        }

        // Si les validations passent, vous pouvez effectuer d'autres actions comme l'envoi du formulaire ou le traitement des dates.
    }
    function selectionDatePayement() {
        var dateDebut = document.getElementById('dateDebutPayement').value;
        var dateFin = document.getElementById('dateFinPayement').value;
        if (dateDebut === '' || dateFin === '') {
            alert('Veuillez sélectionner à la fois une date de début et une date de fin.');
            return;
        }

        if (dateDebut > dateFin) {
            alert('La date de début doit être inférieure ou égale à la date de fin.');
            return;
        }
        if (dateDebut <= dateFin) {
            document.getElementById('etatPayement').submit(); // Exemple pour soumettre le formulaire
            return;
        }

        // Si les validations passent, vous pouvez effectuer d'autres actions comme l'envoi du formulaire ou le traitement des dates.
    }
</script>
@endsection