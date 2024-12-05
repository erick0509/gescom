@extends('layouts.header')
@section('contenuPrincipale')
    <style>
        .fournisseur-list {
            max-height: 300px;
            /* Ajustez la hauteur en fonction de vos besoins */
            overflow-y: auto;
            /* Permet le défilement vertical */
            overflow-x: hidden;
            /* Empêche le défilement horizontal */
        }

        .table th {
            text-align: center;
            /* Centre le texte des en-têtes */
        }

        .no-wrap {
            white-space: nowrap;
            /* Empêche le contenu de se mettre à la ligne */
        }

        .texte-long {
            max-width: 150px;
            /* Définir la largeur maximale */
            /* Activer le défilement si le texte dépasse */
            white-space: normal;
            word-break: break-word;
        }

        .texte-court {
            max-width: 70px;
            /* Définir la largeur maximale */
            /* Activer le défilement si le texte dépasse */
            white-space: normal;
            word-break: break-word;
        }
    </style>
    <section id="article-liste" class="article py-4 ">
        <div class="container">
            <div class="row mt-5 d-flex text-center justify-content-center align-items-center">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif

                @if (session()->has('successDelete'))
                    <div class="alert alert-success">
                        {{ session()->get('successDelete') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="row ">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Articles</h2>
                    <form class="mt-2" method="post" action="{{ route('creer.article') }}">
                        @csrf
                        <label class="form-label h6">Nouvel article</label>

                        <!-- Champs sur la même ligne avec séparation -->
                        <div class="row g-2"> <!-- "g-2" pour espacer un peu les colonnes -->
                            <div class="col-md-4">
                                <input class="form-control form-control-sm" name="designation" type="search"
                                    placeholder="Désignation" aria-label="Search">
                            </div>
                            <div class="col-md-4">
                                <input class="form-control form-control-sm" name="unite" type="text"
                                    placeholder="Unité" aria-label="Unité" title="Pc, Bd, Ct, ...">
                            </div>
                            <div class="col-md-4">
                                <input class="form-control form-control-sm" name="quantitePack" type="number"
                                    placeholder="Qt par Pack" aria-label="Quantité par Pack"
                                    title="Uniquement pour les articles en pièces">
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row g-2 mt-2">
                            <!-- Champ readonly pour afficher le fournisseur sélectionné -->
                            <div class="col-md-4">
                                <input id="fournisseurInput" class="form-control form-control-sm" type="text" readonly
                                    placeholder="Fournisseur sélectionné" aria-label="Fournisseur">
                            </div>

                            <!-- Bouton pour ouvrir le modal de sélection du fournisseur -->
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary btn-sm ms-2" data-bs-toggle="modal"
                                    data-bs-target="#modalFournisseur">
                                    <i class="fas fa-bars"></i>
                                </button>
                            </div>

                            <div class="col-md-4">
                                <button class="btn btn-success btn-sm" type="submit"><i class="fas fa-plus"></i>
                                    Créer</button>
                            </div>
                        </div>

                    </form>

                    <!-- Modal de sélection du fournisseur -->
                    <div class="modal fade" id="modalFournisseur" tabindex="-1" aria-labelledby="modalFournisseurLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalFournisseurLabel">Sélectionner un fournisseur</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label">Choisir un fournisseur :</label>
                                    <ul id="fournisseurList" class="list-group fournisseur-list">
                                        <!-- Liste des fournisseurs -->
                                        @foreach ($fournisseurs as $fournisseur)
                                            <li class="list-group-item list-group-item-action"
                                                data-id="{{ $fournisseur->id }}"
                                                data-intitule="{{ $fournisseur->intitule }}">
                                                {{ $fournisseur->intitule }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="button" class="btn btn-primary"
                                        id="selectFournisseurBtn">Sélectionner</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <h1 class="h2">Liste des Articles <span class="badge bg-secondary">{{ $totalArticle }}</span></h1>
            <div class="row d-flex text-center justify-content-center align-items-center">
                <form action="{{ route('rechercher.article') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
                    @csrf
                    <div class="input-group">
                        <input name="zoneChercher" class="form-control" value="{{ $zoneChercher ?? old('zoneChercher') }}"
                            type="search" placeholder="Recherche..." aria-label="Search">
                        <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            @if ($articles->isEmpty() && $articles->currentPage() === 1)
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
                            @foreach ($articles as $article)
                                <tr style="font-size: 12px;">
                                    <th scope="row" class="texte-long">{{ $article->designation }}</th>
                                    <td class="no-wrap" style="text-align: center;"><b>{{ $article->unite }}</b></td>
                                    <td class="no-wrap" style="text-align: center;"><b>{{ $article->quantitePack }}</b>
                                    </td>
                                    <td class="no-wrap" style="text-align: center;">
                                        <b>{{ $article->created_at->format('d/m/Y') }}</b>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center ">
                                            <!-- Bouton supprimer -->
                                            <form id="form-{{ $article->id }}"
                                                action="{{ route('supprimer.article', $article->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $article->id }})"
                                                    class="btn btn-danger btn-sm me-1 fas fa-trash-alt"></button>
                                            </form>
                                            <!-- Bouton éditer -->
                                            <a onclick="editer(this)" class="btn btn-warning btn-sm edit-btn fas fa-edit"
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
                <div class="modal fade" id="modalCodeAcces" tabindex="-1" aria-labelledby="modalCodeAccesLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                    <b>{{ $articles->currentPage() }}</b>
                </div>
                <div class="row mt-1 d-flex text-center justify-content-start align-items-center">
                    @if (isset($filterBy) && isset($zoneChercher))
                        {{ $articles->appends(['filterBy' => $filterBy, 'zoneChercher' => $zoneChercher])->links() }}
                    @else
                        {{ $articles->links() }}
                    @endif
                </div>
            @endif
            <div class="row mt-1" id="modification" style="display:none;">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
                </div>
                <h1 class="h2"><input type="text" style="border:none;font-weight: bold;" class="form-control"
                        name="titreDesignationModif" disabled></h1>
                <div class="row d-flex text-center justify-content-center align-items-center">
                    <form id="formModif" class="row g-3" method="post">
                        @csrf
                        <div class="mb-3 row">
                            <input type="hidden" name="_method" value="put">
                            <input type="text" class="form-control" id="idModif" name="idModif"
                                style="display:none">
                            <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Désignation:</label>
                            <div class="col-md-2 col-12 mt-1">
                                <input type="text" class="form-control" id="inputPassword" name="designationModif"
                                    readonly>
                            </div>

                            <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Unité:</label>
                            <div class="col-md-2 col-12 mt-1">
                                <input type="text" class="form-control" id="inputPassword" name="uniteModif"
                                    placeholder="Bd,Ct,Ut, ...">
                            </div>

                            <label for="inputPassword" class="col-sm-2 col-form-label h4 mt-1">Quantite en pack(ou en
                                carton):</label>
                            <div class="col-md-2 col-12 mt-1">
                                <input type="number" class="form-control" id="inputPassword" name="quantitePackModif">
                            </div>

                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning mb-3"><i class="fas fa-save"></i>
                                Modifier</button>
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            code_acces: codeAcces
                        })
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
    <!-- Inclure Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Inclure jQuery (Select2 en dépendance) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Inclure Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variable pour stocker l'ID du fournisseur sélectionné
            let selectedFournisseurId = null;
            let selectedFournisseurIntitule = null;

            // Écouteur pour chaque élément de la liste des fournisseurs
            document.querySelectorAll('#fournisseurList li').forEach(function(li) {
                li.addEventListener('click', function() {
                    // Ajouter la classe 'active' à l'élément cliqué, et retirer la classe des autres éléments
                    document.querySelectorAll('#fournisseurList li').forEach(function(item) {
                        item.classList.remove('active');
                    });
                    li.classList.add('active'); // Ajouter 'active' à la ligne sélectionnée

                    // Récupérer les informations du fournisseur sélectionné
                    selectedFournisseurIntitule = li.getAttribute('data-intitule');
                    selectedFournisseurId = li.getAttribute('data-id');
                });
            });

            // Écouteur pour le bouton "Sélectionner" du modal
            document.getElementById('selectFournisseurBtn').addEventListener('click', function() {
                if (selectedFournisseurId && selectedFournisseurIntitule) {
                    // Mettre à jour l'input readonly avec le fournisseur sélectionné
                    document.getElementById('fournisseurInput').value = selectedFournisseurIntitule;

                    // Optionnel : Stocker l'ID du fournisseur dans un champ caché si nécessaire
                    // document.getElementById('fournisseurId').value = selectedFournisseurId;

                    // Fermer le modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalFournisseur'));
                    modal.hide();
                } else {
                    // Si aucun fournisseur n'est sélectionné, afficher un message d'alerte
                    alert("Veuillez sélectionner un fournisseur.");
                }
            });
        });
    </script>
@endsection
