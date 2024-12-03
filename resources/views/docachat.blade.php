@extends("layouts.header")
@section("contenuPrincipale")
<style>
  .table th {
        text-align: center; /* Centre le texte des en-têtes */
    }
    .no-wrap {
        white-space: nowrap; /* Empêche le contenu de se mettre à la ligne */
    }
    .texte-court {
        max-width: 40px; /* Définir la largeur maximale */
         /* Activer le défilement si le texte dépasse */
        white-space: normal;
        word-break: break-word;
    }
    /* CSS pour centrer le modal */
    #modalePayement{
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


</style>
<section  id="article-liste" class="article py-4 ">
  <div class="container">    
        <div class="row mt-5">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2>Documents des achats</h2>
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
        <h1 class="h2">Liste des Achats <span class="badge bg-secondary">{{ $totalFacture}}</span></h1>
        <div class="row d-flex text-center justify-content-center align-items-center">
            <a class="btn btn-primary btn-sm col-md-4 col-sm-6 mb-1" type="submit" href="{{ route('factureAchat') }}">Nouvelle achat</a>
        </div>
        <div class="row d-flex text-center justify-content-center align-items-center">
          <form id="formDate" action="{{ route('rechercherParDate.documentachat') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
            @csrf
            <div class="input-group">
              <input value="{{ $zoneChercherDate ?? date('Y-m-d') }}" id="zoneChercherDate" name="zoneChercherDate" class="form-control" type="date" placeholder="Recherche par date..." aria-label="Search">
            </div>
          </form>
          <form action="{{ route('rechercher.documentachat') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
          @csrf
            <div class="input-group">
              <div class="input-group-append">
                <select class="form-select" aria-label="Filtrer par" name="filterBy" id="filterBy">
                  <option value="id"  @if(isset($filterBy) && $filterBy == 'id') selected @endif>N° Pièce</option>
                  <option value="reference" @if(isset($filterBy) && $filterBy == 'reference') selected @endif>Référence</option>
                  <option value="fournisseur" @if(isset($filterBy) && $filterBy == 'fournisseur') selected @endif>Fournisseur</option>
                </select>
              </div>
              <input value="{{ $zoneChercher ?? old('zoneChercher') }}" id="zoneChercher" name="zoneChercher" class="form-control" type="search" placeholder="Recherche..." aria-label="Search">
              <button class="btn btn-warning" id="btnSearch" type="submit"><i class="fas fa-search"></i></button>
            </div>
          </form>
          <form id="formStatut" action="{{ route('rechercherParStatut.documentachat') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
            @csrf
            <div class="input-group">
              <select class="form-select" aria-label="Filtrer par" name="filterByStatut" id="filterByStatut">
                <option value="tout"  @if(isset($filterByStatut) && $filterByStatut == 'tout') selected @endif>Tout</option>
                <option value="non payee" @if(isset($filterByStatut) && $filterByStatut == 'non payee') selected @endif>Non payee</option>
                <option value="payee" @if(isset($filterByStatut) && $filterByStatut == 'payee') selected @endif>Payee</option>
              </select>
            </div>
          </form>
        </div>
        @if($achatsDepot->isEmpty()&& $achatsDepot->currentPage() === 1)
            <div class="alert alert-danger text-center mt-2" role="alert">
                <p>Les documents de la facture d'achat est vide! Veuillez effectuer des achats</p>.
            </div>
        @else
          <div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">
          <table class="table table-striped table-hover mt-3 ">
            <thead class="table-light">
              <tr>
                <th scope="col">N° Pièce</th>
                <th scope="col">Date Pièce</th>
                <th scope="col">Date d'Achat</th>
                <th scope="col">N° Facture</th>
                <th scope="col">Fournisseur</th>
                <th scope="col">Contact</th>
                <th scope="col">Total TTC</th>
                <th scope="col">Solde Dû</th>
                <th scope="col" class="texte-court">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($achatsDepot as $facture)
                <tr style="font-size: 13px;">
                  <td><b>{{$facture->primaryKey}}</b></td>
                  <td style="text-align: center;"><b>{{\Carbon\Carbon::parse($facture->created_at)->format('d/m/Y')}}</b></td>
                  <td style="text-align: center;"><b>{{\Carbon\Carbon::parse($facture->dateAchat)->format('d/m/Y')}}</b></td>
                  <td><b>{{$facture->ReferenceFactureAchat}}</b></td>
                  <td style="text-align: center;"><b>{{$facture->fournisseur->intitule}}</b></td>
                  <td style="text-align: center;"><b>{{$facture->fournisseur->contact}}</b></td>
                  <td style="text-align: right;" class="no-wrap"><b>{{number_format($facture->montantTotal,1,',',' ')}}</b></td>
                  <td style="text-align: right;" class="no-wrap"><b>{{number_format($facture->montantTotal-$facture->sommePayee,1,',',' ')}}</b></td>
                  <td class="texte-court">
                    <div class="d-flex justify-content-center">
                      <form id="form-{{$facture->id}}" action="{{ route('documentachat.details',['id' => $facture->id, 'page' => $achatsDepot->currentPage()])}}" method="get">
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
              <b>{{$achatsDepot->currentPage()}}</b>
            </div>
            <div class="row mt-1 d-flex text-center justify-content-start align-items-center">
            @if(isset($filterBy) && isset($zoneChercher))
                {{ $achatsDepot->appends(['filterBy' => $filterBy, 'zoneChercher' => $zoneChercher])->links() }}
            @else
                {{ $achatsDepot->links() }}
            @endif
            </div>
        @endif
        <div class="row">
                <div class="col-6 col-md-4 mt-2"> 
                    <a onclick="etatPayement()" class="btn btn-primary col-12 mb-1"><i class="fas fa-list-ul"></i> Paiements </a>  
                </div>
                <div id="modalePayement" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModale()" class="close">&times;</span>
                            <p>Selectionner une periode:</p>
                            <form id="etatPayement" action="{{ route('etatPayement.documentachat')}}" method="POST">
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
    var modalPayement = document.getElementById('modalePayement');
    var btnSearch = document.getElementById('btnSearch');
    function etatPayement(){
        modalPayement.style.display = 'block';
        btnSearch.style.display = 'none';
    }
    function fermerModale() {
        modalPayement.style.display = 'none';
        btnSearch.style.display = 'block';
    }
    window.onclick = function(event) {
        if (event.target == modalPayement) {
          fermerModale();
        }
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