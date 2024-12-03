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
            <h2>Articles</h2>
            <form class="mt-2 col-md-5 col-sm-6" method="post" action="{{ route('creer.article') }}">
                @csrf
                <label class="form-label h6">Nouvelle article</label>
                
                <!-- Champ pour la désignation -->
                <div class="input-group mb-3">
                    <input class="form-control" name="designation" type="search" placeholder="Désignation" aria-label="Search">
                    <input class="form-control" name="unite" type="text" placeholder="Unité" aria-label="Unité" title="Pc, Bd, Ct, ...">
                    <input class="form-control" name="quantitePack" type="number" placeholder="Qt par Pack" aria-label="Quantité par Pack" title="Uniquement pour les articles en pieces">
                    <button class="btn btn-success" type="submit"><i class="fas fa-plus"></i> Creer</button>
                </div>
                
            </form>

            
          </div>    
        </div>
        <h1 class="h2">Liste des Articles <span class="badge bg-secondary">{{ $totalArticle}}</span></h1>
        <div class="row d-flex text-center justify-content-center align-items-center">
            <form action="{{ route('rechercher.article') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
            @csrf  
              <div class="input-group">
                <input name="zoneChercher" class="form-control" value="{{$zoneChercher ?? old('zoneChercher')}}" type="search" placeholder="Recherche..." aria-label="Search">
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
                <tr>
                    <th scope="col">Désignation</th>
                    <th scope="col">Unité</th>
                    <th scope="col">Quantité en Pack</th>
                    <th scope="col">Date de Création</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach($articles as $article)
                <tr style="font-size: 12px;">
                    <th scope="row" class="texte-long">{{ $article->designation }}</th>
                    <td class="no-wrap" style="text-align: center;"><b>{{ $article->unite }}</b></td>
                    <td class="no-wrap" style="text-align: center;"><b>{{ $article->quantitePack }}</b></td>
                    <td class="no-wrap" style="text-align: center;"><b>{{ $article->created_at->format('d/m/Y') }}</b></td>
                    <td>
                        <div class="d-flex justify-content-center ">
                            <!-- Bouton supprimer -->
                            <form id="form-{{ $article->id }}" action="{{ route('supprimer.article', $article->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $article->id }})" 
                                        class="btn btn-danger btn-sm me-1 fas fa-trash-alt"></button>
                            </form>
                            <!-- Bouton éditer -->
                            <a onclick="editer(this)"
                            class="btn btn-warning btn-sm edit-btn fas fa-edit"
                            data-id="{{ $article->id }}"
                            data-designation="{{ $article->designation }}"
                            data-quantitePack="{{ $article->quantitePack }}"
                            data-unite="{{ $article->unite }}">
                            </a>
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
        @endif        
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
                  
                  <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Unité:</label>
                  <div class="col-md-2 col-12 mt-1">
                    <input type="text" class="form-control" id="inputPassword" name="uniteModif" placeholder="Bd,Ct,Ut, ...">
                  </div>
                  
                  <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Quantite en pack(ou en carton):</label>
                  <div class="col-md-2 col-12 mt-1">
                    <input type="number" class="form-control" id="inputPassword" name="quantitePackModif">
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
                        let unite = element.getAttribute('data-unite');
                        let designation = element.getAttribute('data-designation');
                        let quantitePack = element.getAttribute('data-quantitePack');
                        
                        
                        // Mettre à jour les champs du formulaire
                        document.querySelector('input[name="idModif"]').value = id;
                        document.querySelector('input[name="titreDesignationModif"]').value = "#" + designation;
                        document.querySelector('input[name="designationModif"]').value = designation;
                        document.querySelector('input[name="quantitePackModif"]').value = quantitePack;
                        document.querySelector('input[name="uniteModif"]').value = unite;
                        // Afficher la zone de modification
                        // document.getElementById('modification').style.display = 'block';

                        // Faire défiler automatiquement jusqu'à la zone de modification
                        document.getElementById('modification').scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                        const formulaire = document.getElementById('formModif');
                        formulaire.action = `{{ route('modifier.article', ['article' => ':id']) }}`.replace(':id', id);
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

@endsection