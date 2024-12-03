@extends("layouts.master")
@section("contenu")
<style>
    .long-text {
        max-width: 150px; /* Définir la largeur maximale */
         /* Activer le défilement si le texte dépasse */
        white-space: normal;
        word-break: break-word;
    }
    #modale {
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
    @media print {
        
        .entete {
            display: block;
        }
        .table {
            width: 100%;
        }
        .table thead {
            display: table-header-group;
        }
        .table tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
<div id="container">       
            <div class="row text-center justify-content-center  ">
              <div class="col-md-4 mt-1">
              <div class="print-section" id="print-section">
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th colspan="4">
                                <div class="entete">
                                    <div class="border border-2 border-secondary">
                                        <div class="row">
                                            <h1>{{ session('depotValue') }}</h1>
                                        </div>
                                        <div class="row">
                                            <h5 style="font-size: 12px;">{{ $transfert->depotSource->adresse }}</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <h1 class="h5">N°{{$transfert->primaryKey}}</h1>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <p class="my-0"><b>Date de Transfert:</b> {{\Carbon\Carbon::parse($transfert->dateTransfert)->format('d/m/Y')}}</p>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <p class="my-0"><b>Source:</b> {{$transfert->depotSource->intitule}}</p>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <p class="my-0"><b>Destination:</b> {{$transfert->depotDestination->intitule}}</p>
                                    </div>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th scope="col">Qt</th>
                            <th scope="col">Désignation</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articlesTransfert as $article)  
                        <tr>
                            <td>{{ $article->quantiteAffichee }}</td>
                            <td class="long-text" style="text-align: left;">{{ $article->article->designation }}</td>
                            
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="4">
                                <div class="d-flex justify-content-start">
                                    <p class="me-3 mt-3"><b>N.B</b>: {{$transfert->commentaire}}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
                <div class="row">
                    <select class="form-select form-select-sm mt-3 mb-3" id="modeImpression">
                        <option value="Mode ticket">Impression ticket</option>        
                        <option value="Mode grand format">Impression A5</option>
                    </select>
                    
                    @if($transfert->statut==='en attente' && $transfert->depotSource->intitule === session('depotValue'))
                            <div class="col-4 mt-2">
                                <a class="btn btn-primary col-12 mb-1" href="{{ route('modifier.documentTransfert', ['id' => $transfert->id])}}"> </i>Modifier</a>
                            </div>
                        <div class="col-4 mt-2">
                            <form id="formulaire" action="{{ route('supprimer.documentTransfert',$id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <a onclick="confirmDelete()" class="btn btn-danger col-12 mb-1"><i class="fas fa-trash-alt"></i></a>  
                            </form>
                        </div>  
                    @endif
                    @if($transfert->statut==='en attente' && $transfert->depotDestination->intitule === session('depotValue'))
                    
                        <div class="col-4 mt-2">
                            <form id="confirmerFacture" action="{{route('confirmer.transfert', ['id' => $id])}}"  method ="POST" >
                                @csrf
                                @method('POST')             
                                <a type="button" onclick="if (confirm('Voulez-vous vraiment confirmer ce Transfert?')){document.getElementById('confirmerFacture').submit();}"
                                class="btn btn-success col-12 mb-1" ><i class="fas fa-check-circle"></i> Confirmer</a>
                            </form>
                        </div>
              
                    @endif
                
                    <!--
                    
                    -->
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
                    
                    <div class="col-4 mt-2">
                        <a class="btn btn-secondary col-12 mb-1" href="{{ route('listeTransfert', ['page' => $page])}}"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <div class="row">
                  <div class="col-4 mt-2">
                    <button onclick="window.print()" class="btn btn-primary col-12 mb-1"><i class="fas fa-print"></i></button>
                  </div>
                </div>
              </div>
            </div>            
          
</div>
<script>
      function confirmDelete() {      
                        if (confirm('Voulez-vous vraiment supprimer ce Transfert ?')) 
                        {
                            document.getElementById('formulaire').submit(); 
                        }                      
        }
      function imprimer(element){
            var Facture = document.body.innerHTML;
            // Imprimer le contenu requis
            var printContent = document.getElementById(element).innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = Facture;
      }
      // Récupérer la fenêtre modale
    var modal = document.getElementById('modale');

    // Fonction pour afficher la fenêtre modale
    function afficherModale() {
        modal.style.display = 'block';
    }

    // Fonction pour fermer la fenêtre modale
    function fermerModale() {
        modal.style.display = 'none';
    }
    function choisirComptant() {
        var modalAuComptant = document.getElementById("modalAuComptant");
        modalAuComptant.style.display = "block";        
    }
    function confirmAcomptant(element) {
        // Soumettre le formulaire
        var mode= document.querySelector('input[name="modeAc"]');
        if(mode.value.trim()===''){
            alert('Veuiller remplir la formulaire');
        }
        else{
            if (confirm('voulez-vous confirmer cette règlegement de compte?')){
                document.getElementById("formSommePayeeAc").submit();
            }
        } 
        
    }

    function confirmCredit(event,element) {
        // Soumettre le formulaire
        // Soumettre le formulaire
        event.preventDefault();
        var sommePayee= document.querySelector('input[name="sommePayee"]');
        var mode= document.querySelector('input[name="mode"]');
        var sommeTotalElement = document.getElementById('sommeTotal');

        // Récupérer la valeur de l'attribut data-somme-total
        var sommeTotalData = sommeTotalElement.getAttribute('data-somme-total');

        // Convertir la valeur en nombre flottant
        var sommeTotalFloat = parseFloat(sommeTotalData.replace(/\s/g, '').replace(',', '.'));
        var sommePayeeFloat = parseFloat(sommePayee.value.replace(/\s/g, '').replace(',', '.'));

        // Utiliser la valeur flottante récupérée (sommeTotalFloat)
        if(mode.value.trim()==='' || sommePayee.value.trim()===''){
            alert('Veuiller remplir la formulaire');
        }
        else{
            if (isNaN(sommePayeeFloat)) {
                alert('La somme payée doit être un nombre valide.');
            } else if (sommePayeeFloat < 0) {
                alert('La somme payée doit être supérieure à zéro.');
            } else if (sommePayeeFloat > sommeTotalFloat) {
                alert('La somme payée ne peut pas être supérieure à la somme restant.');
            } else {
                if (confirm('voulez-vous confirmer cette règlegement de compte?')){
                    document.getElementById("formSommePayee").submit();
                }               
            }
        } 
    }

    // Fonction pour choisir "A crédit"
    function choisirCredit() {
        var modalACredit = document.getElementById("modalACredit");
        modalACredit.style.display = "block";
    }

    // Fermer la fenêtre modale lorsque l'utilisateur clique en dehors de celle-ci
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
    function fermerModaleACredit() {
    var modalACredit = document.getElementById("modalACredit");
    modalACredit.style.display = "none";
    }
    function fermerModaleAuComptant() {
        var modalAuComptant = document.getElementById("modalAuComptant");
        modalAuComptant.style.display = "none"; 
    }
    window.onclick = function(event) {
        var modalACredit = document.getElementById("modalACredit");
        if (event.target == modalACredit) {
            modalACredit.style.display = "none";
        }
    }
</script>
<script>
    document.getElementById('modeImpression').addEventListener('change', function() {
        var printSection = document.querySelector('.print-section');
        var selectedMode = this.value;

        // Supprimez toutes les classes de mode d'impression
        printSection.classList.remove('grand-format', 'ticket');

        // Ajoutez la classe appropriée en fonction de la sélection
        if (selectedMode === 'Mode grand format') {
            printSection.classList.add('grand-format');
        } else if (selectedMode === 'Mode ticket') {
            printSection.classList.add('ticket');
        }
    });
</script>
          @endsection