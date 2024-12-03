@extends("layouts.header")
@section("contenuPrincipale")
<style>
    .table th {
        text-align: center; /* Centre le texte des en-têtes */
    }
    .no-wrap {
        white-space: nowrap; /* Empêche le contenu de se mettre à la ligne */
    }
    .texte-long {
        max-width: 150px; /* Définir la largeur maximale */
         /* Activer le défilement si le texte dépasse */
        white-space: normal;
        word-break: break-word;
    }
    .texte-court {
        max-width: 70px; /* Définir la largeur maximale */
         /* Activer le défilement si le texte dépasse */
        white-space: normal;
        word-break: break-word;
    }
</style>
@php
    $depot = \App\Models\Depot::where('intitule', session('depotValue'))->first();
@endphp
<section  id="article-liste" class="article py-4 ">
  <div class="container"> 
        <div class="row mt-5 d-flex text-center justify-content-center align-items-center">
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
            @if(session()->has("error"))
                <div class="alert alert-danger">
                    {{session()->get('error')}}
                </div>
            @endif

            @if($errors->any())
                <ul class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            @endif
            
        </div>   
        <div class="row ">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <div class="row">
              <div class="col-md-2">
                <h2>Articles</h2>
              </div>
              @if($depot && $depot->principal == 1)             
                  <!-- Section "Nouvelle article pour ce Depot" -->
                  <div class="col-md-10">
                      <!-- Champ désignation en lecture seule -->
                      <div class="row">
                          <div class="col-md-6 mb-1">
                              <input id="desi" class="form-control form-control-sm" type="search" placeholder="..." aria-label="Search" readonly>
                          </div>
                      </div>
                      
                      <div class="row">
                          <!-- Formulaire pour ajouter un nouvel article -->
                          <div class="col-md-4">
                              <form class="mt-2" method="post" action="{{route('ajouter.article')}}">
                                  @csrf
                                  <label class="form-label h6">Nouvel article pour ce Dépôt</label>
                                  <input type="hidden" name="codeValide" value="{{ $codeValide }}">
                                  <div class="input-group input-group-sm mb-3">
                                      <!-- Champ pour la désignation -->
                                      <input id="designation" class="form-control form-control-sm" type="search" placeholder="Désignation..." aria-label="Search">
                                      
                                      <!-- Liste déroulante des articles -->
                                      <select id="selectDesignation" class="form-select form-select-sm" aria-label="ListeArticles" name="article_id">
                                          <option value="0" selected>...</option>
                                          @foreach ($allArticles as $article)
                                              <option value="{{ $article->id }}">{{ $article->designation }}</option>
                                          @endforeach
                                      </select>

                                      <!-- Bouton Ajouter -->
                                      <button class="btn btn-success btn-sm" type="submit" id="btnAjouterArticle" disabled>
                                          <i class="fas fa-plus"></i> Ajouter
                                      </button>
                                  </div>
                              </form>
                          </div>

                          <!-- Section Importer dans CSV -->
                          <div class="col-md-4">
                              <form class="mt-2" action="{{ route('import.article') }}" method="POST" enctype="multipart/form-data">
                                  @csrf
                                  <label class="form-label h6">Importer dans CSV</label>
                                  <div class="input-group input-group-sm mb-3">
                                      <input class="form-control form-control-sm" type="file" name="file" accept=".csv">
                                      <button type="submit" class="btn btn-primary btn-sm">
                                          <i class="fas fa-upload"></i> Importer
                                      </button>
                                  </div>
                              </form>
                          </div>
                      </div>
                  </div>
                            
              @endif
            </div>
          </div>    
        </div>
        
        <h1 class="h2">Liste des Articles <span class="badge bg-secondary">{{ $totalArticle}}</span></h1>
        <div class="row d-flex text-center justify-content-center align-items-center">
            <form action="{{ route('rechercher2.article') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
              @csrf
              <!-- Champ caché pour passer codeValide -->
              <input type="hidden" name="codeValide" value="{{ $codeValide }}">
              
              <div class="input-group">
                  <div class="input-group-append">
                      <select class="form-select" aria-label="Filtrer par" name="filterBy">
                          <option value="designation" @if(isset($filterBy) && $filterBy == 'designation') selected @endif>Designation</option>
                          <option value="quantiteEgal" @if(isset($filterBy) && $filterBy == 'quantiteEgal') selected @endif>Quantité égale à</option>  
                          <option value="quantiteInferieur" @if(isset($filterBy) && $filterBy == 'quantiteInferieur') selected @endif>Quantité inférieur à</option>  
                          <option value="quantiteSuperieur" @if(isset($filterBy) && $filterBy == 'quantiteSuperieur') selected @endif>Quantité supérieur à</option>  
                      </select>
                  </div>
                  <input name="zoneChercher" class="form-control" value="{{ $zoneChercher ?? old('zoneChercher') }}" type="search" placeholder="Recherche..." aria-label="Search">
                  <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
              </div>
          </form>

        </div>
        @if($articles->isEmpty()&& $articles->currentPage() === 1)
            <div class="alert alert-danger text-center mt-2" role="alert">
                <p>Pas d'article à gérer pour le moment! Veuillez ajouter un nouveau ou faire des achats</p>.
            </div>
        @else
        <div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">
        <table class="table table-striped table-hover mt-3 ">
            <thead class="table-light">
                <!-- Première ligne d'en-tête -->
                <tr>
                    <th scope="col" rowspan="2">Désignation</th>
                    <th scope="col" rowspan="2">Stock</th>
                    <th scope="col" rowspan="2">Prix d'achat</th>
                    <th scope="col" colspan="3" class="table-active">Tarif 1</th>
                    <th scope="col" colspan="3" class="table-active">Tarif 2</th>
                    <th scope="col" colspan="3" class="table-active">Tarif 3</th>
                    <th scope="col" rowspan="2">Actions</th>
                </tr>
                <!-- Deuxième ligne d'en-tête -->
                <tr class="text-secondary">
                    <th scope="col">Marge/ Prix d'achat</th>
                    <th scope="col">Prix</th>
                    <th scope="col">Qt</th>
                    <th scope="col">Marge/ Prix d'achat</th>
                    <th scope="col">Prix</th>
                    <th scope="col">Qt</th>
                    <th scope="col">Marge/ Prix d'achat</th>
                    <th scope="col">Prix</th>
                    <th scope="col">Qt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articles as $article)
                <tr style="font-size: 12px;">
                    <th scope="row" class="texte-long">{{ $article->designation }}</th>
                    <td class="no-wrap" style="text-align: center;"><b>{{ $article->stocks->first()->quantiteDepot ?? 'Non disponible' }}</b></td>
            
              <!--      <td class="no-wrap" style="text-align: right;"><b>{{ number_format($article->stocks->first()->prixAchat, 1, ',', ' ') }}</b></td>-->
                    <td class="no-wrap" style="text-align: right;">
                      @if($codeValide)
                          <b>{{ number_format($article->stocks->first()->prixMoyenAchat, 1, ',', ' ') }}</b>
                      @endif</td>

                    <!-- Affichage des tarifs -->
                    @foreach(range(0, 2) as $index)
                        @if(isset($article->tarifs[$index]))
                            @php
                                $tarif = $article->tarifs[$index];
                            @endphp
                            <!-- Affichage des informations tarifaires -->
                            <td class="no-wrap" style="text-align: right;">
                                @if($codeValide)
                                    <b>{{ number_format(max($tarif->prix - ($article->stocks->first()->prixMoyenAchat ?? 0),0), 1, ',', ' ') }}</b>
                                @endif
                                
                            </td>
                            <td class="no-wrap" style="text-align: right;">
                                    <b>{{ number_format($tarif->prix, 1, ',', ' ') }}</b>
                             
                                
                            </td>
                            <td class="no-wrap" style="text-align: center;">
                                <b>[{{ $tarif->quantite_min }} - {{ $tarif->quantite_max === 9999999 ? 'infini' : $tarif->quantite_max }}]</b>
                            </td>
                        @else
                            <!-- Afficher des cellules vides si aucun tarif n'existe pour cet index -->
                            <td class="no-wrap" style="text-align: right;"></td>
                            <td class="no-wrap" style="text-align: right;"></td>
                            <td class="no-wrap" style="text-align: center;"></td>
                        @endif
                    @endforeach

                    <td>
                        <div class="d-flex justify-content-center ">
                            <form id="form-{{ $article->id }}" action="{{ route('detacher.article', $article->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $article->id }})" class="btn btn-danger btn-sm me-1 fas fa-trash-alt"></button>
                            </form>
                            @if($codeValide)
                                    <a onclick="editer(this)"
                                      class="btn btn-warning btn-sm edit-btn fas fa-edit"
                                      data-id="{{ $article->id }}"
                                      data-designation="{{ $article->designation }}"
                                      data-quantiteStock="{{ $article->stocks->first()->quantiteDepot }}"
                                      data-prixAchat="{{ $article->stocks->first()->prixMoyenAchat }}"
                                    
                                      @foreach($article->tarifs as $index => $tarif)
                                          data-tarif-prix-{{ $index }}="{{ $tarif->prix }}"
                                          data-tarif-quantite-min-{{ $index }}="{{ $tarif->quantite_min }}"
                                          data-tarif-quantite-max-{{ $index }}="{{ $tarif->quantite_max }}"
                                      @endforeach
                                    ></a>
                                @endif
                            
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
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

            <div class="row mt-2 d-flex text-center justify-content-center align-items-center">
                <b>{{$articles->currentPage()}}</b>
            </div>
            <div class="row mt-1 d-flex text-center justify-content-start align-items-center">
            @if(isset($filterBy) && isset($zoneChercher))    
                {{ $articles->appends(['filterBy' => $filterBy, 'zoneChercher' => $zoneChercher])->links() }}
            @else
              {{$articles->links()}}
            @endif
            </div>
            <!-- Bouton pour imprimer la liste complète -->
            <a href="{{ route('articles.impression', ['codeValide' => $codeValide]) }}" target="_blank" class="btn btn-primary mb-3"><i class="fas fa-print"></i> Imprimer</a>

        @endif        <!-- DISPLAY NONE -->
          <div class="row mt-1" id="modification" style="display:none;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
            </div> 
            <h1 class="h2"><input type="text" style="border:none;font-weight: bold;" class="form-control" name="titreDesignationModif" disabled></h1>
            <div class="row d-flex text-center justify-content-center align-items-center">
              <form id="formModif" class="row g-3" method="post">
              @csrf  
                <div class="mb-3 row">
                <input type="hidden" name="_method" value="put">
                <input type="text" class="form-control" id="idModif" name="idModif" style="display:none"> 
                  <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Désignation:</label>
                  <div class="col-md-2 col-12 mt-1">
                    <input type="text" class="form-control" id="inputPassword" name="designationModif" readonly>
                  </div>
                  <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Prix d'achat:</label>
                  <div class="col-md-2 col-12 mt-1">
                  <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text"><b>Ar</b></span>
                          <input type="number" class="form-control" id="inputPassword" name="prixAchatModif" step="0.01" required>
                        </div>
                  </div>
                 
                  <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Quantité disponnible:</label>
                  <div class="col-md-2 col-12 mt-1">
                    <input type="number" class="form-control" id="inputPassword" step="0.01" name="quantiteStockModif">
                  </div>
                 
                  <div class="row mt-3">
                    <div class="col-md-4">
                      <div class="border p-3">
                        <label for="inputPassword" class="col-form-label h4 mt-1">Tarif 1:</label>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Prix:</span>
                          <input type="number" class="form-control" id="inputPassword" name="prixVenteModif1" placeholder="1500" step="0.01">
                        </div>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Min:</span>
                          <input type="number" class="form-control" id="inputPassword" step="0.01" name="quantiteMinModif1" placeholder="1">
                        </div>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Max:</span>
                          <input type="number" class="form-control" id="inputPassword" step="0.01" name="quantiteMaxModif1" placeholder="9">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="border p-3">
                        <label for="inputPassword" class="col-form-label h4 mt-1">Tarif 2:</label>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Prix:</span>
                          <input type="number" class="form-control" id="inputPassword"  name="prixVenteModif2" placeholder="1400" step="0.01">
                        </div>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Min:</span>
                          <input type="number" class="form-control" id="inputPassword"step="0.01"  name="quantiteMinModif2" placeholder="10">
                        </div>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Max:</span>
                          <input type="number" class="form-control" id="inputPassword" step="0.01"name="quantiteMaxModif2" placeholder="29">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="border p-3">
                        <label for="inputPassword" class="col-form-label h4 mt-1">Tarif 3:</label>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Prix:</span>
                          <input type="number" class="form-control" id="inputPassword" name="prixVenteModif3" placeholder="1300" step="0.01">
                        </div>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Min:</span>
                          <input type="number" class="form-control"step="0.01" id="inputPassword" name="quantiteMinModif3" placeholder="30">
                        </div>
                        <div class="input-group mt-1">
                          <span for="inputPassword" class="input-group-text">Max:</span>
                          <input type="number" class="form-control" step="0.01" id="inputPassword" name="quantiteMaxModif3" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end">
                  <button type="submit" class="btn btn-warning mb-3" ><i class="fas fa-save"></i> Modifier</button>                 
                </div>
              </form>
            </div>
          </div>
      </div>
  </div>
</section>

<script>

    function editer(element) {
        //var codeAcces = prompt('Entrez le code d\'accès :'); // Demander le code d'accès
       document.getElementById('modification').style.display = 'block';
                        let id = element.getAttribute('data-id');
    
                        let designation = element.getAttribute('data-designation');
                        let quantiteStock = element.getAttribute('data-quantiteStock');
                        
                        let prixAchat = element.getAttribute('data-prixAchat');
                        let prix1 = element.getAttribute('data-tarif-prix-0');
                        let quantiteMin1 = element.getAttribute('data-tarif-quantite-min-0');
                        let quantiteMax1 = element.getAttribute('data-tarif-quantite-max-0');
                        let prix2 = element.getAttribute('data-tarif-prix-1');
                        let quantiteMin2 = element.getAttribute('data-tarif-quantite-min-1');
                        let quantiteMax2 = element.getAttribute('data-tarif-quantite-max-1');
                        let prix3 = element.getAttribute('data-tarif-prix-2');
                        let quantiteMin3 = element.getAttribute('data-tarif-quantite-min-2');
                        let quantiteMax3 = element.getAttribute('data-tarif-quantite-max-2');
                        // Mettre à jour les champs du formulaire
                        document.querySelector('input[name="idModif"]').value = id;
                        document.querySelector('input[name="titreDesignationModif"]').value = "#" + designation;
                        document.querySelector('input[name="designationModif"]').value = designation;
                        document.querySelector('input[name="prixAchatModif"]').value = prixAchat;
                        document.querySelector('input[name="quantiteStockModif"]').value = quantiteStock;
                        document.querySelector('input[name="prixVenteModif1"]').value = prix1;
                        document.querySelector('input[name="quantiteMinModif1"]').value = quantiteMin1;
                        document.querySelector('input[name="quantiteMaxModif1"]').value = quantiteMax1;
                        document.querySelector('input[name="prixVenteModif2"]').value = prix2;
                        document.querySelector('input[name="quantiteMinModif2"]').value = quantiteMin2;
                        document.querySelector('input[name="quantiteMaxModif2"]').value = quantiteMax2;
                        document.querySelector('input[name="prixVenteModif3"]').value = prix3;
                        document.querySelector('input[name="quantiteMinModif3"]').value = quantiteMin3;
                        document.querySelector('input[name="quantiteMaxModif3"]').value = quantiteMax3;
                       
                       
                        // Afficher la zone de modification
                        // document.getElementById('modification').style.display = 'block';

                        // Faire défiler automatiquement jusqu'à la zone de modification
                        document.getElementById('modification').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        const formulaire = document.getElementById('formModif');
                        formulaire.action = `{{ route('modifier2.article', ['article' => ':id']) }}`.replace(':id', id);
        ////
    }

   
    function confirmDelete(articleId) {
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
                        if (confirm('Voulez-vous vraiment supprimer l\'article?')) {
                          document.getElementById('form-' + articleId).submit();
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
    }
</script>
<!--GESTION D'EVENEMENT SUR L'INPUT -->
<script>
            document.addEventListener('DOMContentLoaded', function() { 
                var btnAjouter = document.getElementById('btnAjouterArticle');              
                var designationInput= document.getElementById('designation');
                var selectDesignation = document.getElementById('selectDesignation');
                const inputDesi = document.getElementById('desi');
                function updateReadonlyState() {
                    if (selectDesignation.value !== selectDesignation.options[0].value) {
                        btnAjouter.disabled = false;
                        var designation = selectDesignation.options[selectDesignation.selectedIndex].text;
                    } else {
                        btnAjouter.disabled = true;
                    }
                }
                designationInput.addEventListener('input', function() {
                    var inputValue = designationInput.value.toLowerCase();
                    var options = selectDesignation .options;
                    var foundMatch = false;
                    for (var i = 0; i < options.length; i++) {
                        var optionText = options[i].text.toLowerCase();

                        if (optionText.includes(inputValue)) {
                          selectDesignation.value = options[i].value;
                          inputDesi.value =options[i].text ;
                            foundMatch = true;
                            updateReadonlyState();
                            break;
                        }
                    }

                    if (!foundMatch) {
                      selectDesignation.value = options[0].value;
                      inputDesi.value =options[0].text ;
                      updateReadonlyState();
                    }
                    
                });
                selectDesignation.addEventListener('change', function() {
                    updateReadonlyState();
                    inputDesi.value = selectDesignation.options[selectDesignation.selectedIndex].text; // Mettre à jour l'input avec la sélection
                });
                updateReadonlyState();
            });
    </script>

@endsection