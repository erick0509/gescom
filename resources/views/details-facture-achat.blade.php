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
</style>
<div id="container"> 
      
            <div class="row text-center justify-content-center  ">
              <div class="col-md-4 mt-1">
                <div class="print-section" id="print-section">
                  <div class=" border border-2 border-secondary">
                    <div class="row">
                      <h1>{{ session('depotValue') }}</h1>
                    </div>
                    <div class="row">
                      <h5 style="font-size: 12px;">{{ $facture->depots->adresse }}</h5>
                    </div>
                  </div>
                  <div class="d-flex justify-content-start">
                      <h1 class="h5">N° {{$facture->primaryKey}}</h1>
                  </div>
                  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
                  </div> 
                  <div class="d-flex justify-content-start">
                      <p class="my-0"><b>Date de Facturation:</b> {{\Carbon\Carbon::parse($facture->dateAchat)->format('d/m/Y')}}</p>
                  </div>
                  <div class="d-flex justify-content-start">
                      <p class="my-0"><b>Référence:</b> {{$facture->ReferenceFactureAchat}}</p>
                  </div>
                  <div class="d-flex justify-content-start">
                      <p class="my-0"><b>Fournisseur:</b> {{$facture->fournisseur->intitule}}</p>
                  </div>
                  <div class="d-flex justify-content-start">
                      <p class="my-0"><b>Contact:</b> {{$facture->fournisseur->contact}}</p>
                  </div>
                  <table class="table mt-3">
                    <thead>
                      <tr>
                          <th scope="col">Qt</th>
                        <th scope="col">Désignation</th>
                        <th scope="col">P.Achat</th>
                        
                        <th scope="col">Montant</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($articlesFacture as $article)  
                      <tr>
                           <td>{{ $article->quantite }}</td>
                        <td class="long-text" style="text-align: left;">
                            {{
                               $article->article->designation
                            }}
                            
                        </td>
                        <td class="no-wrap">{{ number_format($article->prixUnitaire, 0, ',', ' ')  }}</td>
                       
                        <td class="no-wrap" style="text-align: right;">{{ number_format($article->quantite * $article->prixUnitaire ,0,',',' ') }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <div class="d-flex justify-content-start">
                      <p class="me-3 mt-3">Total: {{number_format($sommeTotal,1,',',' ')}} Ar</p>
                  </div>
                  <div class="d-flex justify-content-start">
                    <p class="me-3 ">Somme payée: {{number_format($facture->sommePayee,1,',',' ')}} Ar</p>
                </div>
                <div class="d-flex justify-content-start">
                    <p class="me-3 " id="sommeTotal" data-somme-total="{{$sommeTotal-$facture->sommePayee}}">Reste à payée: {{number_format($sommeTotal-$facture->sommePayee,1,',',' ')}} Ar</p>
                </div>
                  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center border-bottom">
                  </div>
                </div>
              <div class="row">
               <select class="form-select form-select-sm mt-3 mb-3" id="modeImpression">
                    <option value="Mode ticket">Impression ticket</option>    
                    <option value="Mode grand format">Impression A5</option>
                </select>
              @if($facture->statut === 'non payee')
                      <div class="col-4 mt-2"> <!-- href="{{ route('factureVente.pdf', ['id' => $id]) }}" -->   
                          <a type="button" onclick="afficherModale()" class="btn btn-primary col-12 mb-1"><small><i class="fas fa-dollar-sign"></i>Reglement</small></a>                      
                      </div>
                      <div id="modale" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModale()" class="close">&times;</span>
                            <p>Veuillez choisir le type de reglement :</p>                        
                            <button onclick="choisirComptant()" class="btn btn-primary mb-2 form-control">Au comptant</button>                                                 
                            <button onclick="choisirCredit()" class="btn btn-primary mb-2 form-control">A crédit</button>              
                            <button onclick="fermerModale()" class="btn btn-danger">Annuler</button>
                        </div>
                    </div>
                    <div id="modalAuComptant" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModaleAuComptant()" class="close">&times;</span>
                            <p><b>$ REGLEMENT $</b></p>
                            <form id="formSommePayeeAc" action="{{ route('reglementAcomptant.factureAchat', ['id' => $id,'page' => $page]) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="sommePayee" class="form-label"><b>Mode de paiement :</b></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="en Espèce" class="form-control" id="modeAc" name="modeAc" required>
                                    </div>
                                    <label for="sommePayee" class="form-label"><b>Référence : (optionnel)</b></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="CHEQUE N°" class="form-control" id="referenceAc" name="referenceAc" >
                                    </div>
                                </div>
                                <button onclick="confirmAcomptant('container')" class="btn btn-primary">Valider</button>
                            </form>
                        </div>
                    </div>
                    <div id="modalACredit" class="modal ">
                        <div class="modal-content">
                            <span onclick="fermerModaleACredit()" class="close">&times;</span>
                            <p><b>$ REGLEMENT $</b></p>
                            <form id="formSommePayee" action="{{  route('reglementAcredit.factureAchat', ['id' => $id,'page'=>$page]) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="sommePayee" class="form-label"><b>Mode de paiement :</b></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="en Espèce" class="form-control" id="mode" name="mode" required>
                                    </div>
                                    <label for="sommePayee" class="form-label"><b>Référence : (optionnel)</b></label>
                                    <div class="input-group">
                                        <input type="text" placeholder="CHEQUE N°" class="form-control" id="reference" name="reference" >
                                    </div>
                                    <label for="sommePayee" class="form-label"><b>Somme payée :</b></label>
                                    <div class="input-group">
                                        <span for="inputPassword" class="input-group-text">Ar</span>
                                        <input type="number" class="form-control" id="sommePayee" name="sommePayee" required>
                                    </div>
                                </div>
                                <button onclick="confirmCredit(event,'container')" class="btn btn-primary">Valider</button>
                            </form>
                        </div>
                    </div>
                    @endif
                  <div class="col-4 mt-2">
                      <a onclick="window.print()" class="btn btn-primary col-12 mb-1"><i class="fas fa-print"></i></a>
                  </div>
                  <div class="col-4 mt-2">
                      <form id="formulaire" action="{{ route('supprimer.documentachat',$id) }}" method="post">
                          @csrf
                          @method('DELETE')
                          <a onclick="confirmDelete()" class="btn btn-danger col-12 mb-1"><i class="fas fa-trash-alt"></i></a>  
                      </form>
                  </div>
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
                      <a class="btn btn-secondary col-12 mb-1" href="{{ route('documentachat', ['page' => $page])}}"><i class="fas fa-arrow-left"></i></a>
                  </div>
              </div>
              </div>
            </div>            
          
</div>
<script>
      
        function confirmDelete() {
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
                      if (confirm('Voulez-vous vraiment supprimer cette Facture  d\'Achat?')) 
                      {
                          document.getElementById('formulaire').submit(); 
                      }
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