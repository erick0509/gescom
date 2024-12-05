@extends('layouts.master')
@section('contenu')
    <section class="banner d-flex text-center text-light justify-content-center align-items-center position-relative">
        <div class="container">
            <div class="row ">

                <div class="col-md-12 pt-3 mt-4">
                    <!-- Header Contetnt -->
                    <div class="content-block">
                        <h1>Gestion commerciale en toute simplicite </h1>
                        <p>Bienvenue, vous pouvez gerer vos Achat, Vente ,Caisse en choisissant d'abord votre zone de Depot
                            ou Magasin</p>
                    </div>
                    <!-- Advance Search -->
                </div>
            </div>
            <div class="row justify-content-center">
                <form action="{{ route('rechercher.depot') }}" method="GET" class="mt-1 col-md-4 col-sm-6">
                    @csrf
                    <div class="input-group">
                        <input class="form-control" type="search" name="intituleChercher"
                            placeholder="Chercher un depot/Magasin" aria-label="Search"
                            value="{{ $intitule ?? old('intituleChercher') }}">
                        <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

            <form id="logout-form" action="{{ route('auth.logout') }}" method="post"
                class="position-absolute top-0 end-0 mt-2 me-3">
                @csrf
                <button type="submit" class="btn btn-link btn-connecter btn-jaune btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> Se Déconnecter
                </button>
            </form>


            <form id="parametre" action="{{ route('auth.parametre') }}" method="get"
                class="position-absolute top-0 start-0 mt-2 ms-3">
                @csrf
                <a class="btn btn-link text-light" href="#" onclick="allerVersParametre()"><i
                        class="fas fa-cog me-2"></i>Paramètre</a>
            </form>


        </div>
        <div class="modal fade" id="modalCodeAcces" tabindex="-1" aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark" id="modalCodeAccesLabel">Code d'accès</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="codeAccesInput" class="form-label text-dark">Entrez le code d'accès :</label>
                        <input type="password" class="form-control" id="codeAccesInput">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button id="validerCodeAcces" type="button" class="btn btn-primary">Valider</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- vaovao -->
        <div class="modal fade" id="modalCodeAccesDepot" tabindex="-1" aria-labelledby="modalCodeAccesLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark" id="modalCodeAccesLabel">Code d'accès du depot</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="codeAccesInput" class="form-label text-dark">Entrez le code d'accès:</label>
                        <input type="password" class="form-control" id="codeAccesInputDepot">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button id="validerCodeAccesDepot" type="button" class="btn btn-primary">Valider</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="article-liste" class="article py-4 ">
        <div class="container">
            <div class="row d-flex text-center justify-content-center align-items-center">
                <div class="col-md-6 col-sm-12 py-0">
                    <h1>Voici la Liste de votre Depot/ Magasin</h1>
                    <p>vous devez choisir quel Depot/ Magazin voulez-vous gerer</p>

                </div>
            </div>
            <div class="row d-flex text-center justify-content-center align-items-center">
                @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form class="mt-2 col-md-5 col-sm-6" method="post" action="{{ route('creer.depot') }}">
                    @csrf
                    <label class="form-label h6">Nouveau depot</label>
                    <div class="input-group">
                        <input name="intitule" class="form-control" type="search" placeholder="Intitule"
                            aria-label="Search">
                        <input name="prefixe" class="form-control" type="search" placeholder="Prefixe"
                            aria-label="Search">
                        <select name="type_depot" class="form-select">
                            <option value="principal">Principal</option>
                            <option value="secondaire">Secondaire</option>
                        </select>
                        <button class="btn btn-success " type="submit"><i class="fas fa-plus"></i></button>
                    </div>
                </form>
            </div>
            @if ($depots->isEmpty() && $depots->currentPage() === 1)
                <div class="alert alert-danger text-center mt-2" role="alert">
                    <p>Pas de Magasin/ Depot à gérer pour le moment! Veuillez ajouter un nouveau</p>.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">
                    @foreach ($depots as $depot)
                        <div class="col-md-4 col-sm-8 mt-2">
                            <div class="card text-dark">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a style="color:gray;" class="titreDepot" href="{{ route('menu') }}"
                                            onclick="event.preventDefault(); openModalWithDepot('{{ $depot->intitule }}');">
                                            {{ $depot->intitule }}
                                        </a>
                                    </h5>
                                    <form id="formDepot" action="{{ route('menu') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        <input type="hidden" id="depotValue" name="depotValue" value="">
                                    </form>
                                    <p class="card-text" style="font-size:12px;">Creer le
                                        {{ Carbon\Carbon::parse($depot->created_at)->format('d/m/Y') }}</p>
                                    <p class="card-text" style="font-size:12px;">{{ $depot->type }}</p>
                                    <p class="card-text" style="font-size:12px;">{{ $depot->adresse }}</p>
                                </div>
                                <div class="d-flex justify-content-center mb-2">
                                    <form id="form-{{ $depot->id }}"
                                        action="{{ route('supprimer.depot', $depot->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $depot->id }})"
                                            class="btn btn-danger btn-sm me-1 fas fa-trash-alt"></button>
                                    </form>
                                    <a class="btn btn-warning btn-sm edit-btn fas fa-edit" data-id="{{ $depot->id }}"
                                        data-intitule="{{ $depot->intitule }}" data-type="{{ $depot->type }}"
                                        data-adresse="{{ $depot->adresse }}" data-prefixe="{{ $depot->prefixe }}">
                                    </a>
                                </div>
                            </div>
                            <div class="modal fade" id="modalCodeAcces" tabindex="-1"
                                aria-labelledby="modalCodeAccesLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalCodeAccesLabel">Code d'accès</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label for="codeAccesInput" class="form-label">Entrez le code d'accès
                                                :</label>
                                            <input type="password" class="form-control" id="codeAccesInput">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Fermer</button>
                                            <button id="validerCodeAcces" type="button"
                                                class="btn btn-primary">Valider</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="row mt-2 d-flex text-center justify-content-center align-items-center">
                    <b>{{ $depots->currentPage() }}</b>
                </div>
                <div class="row mt-1 d-flex text-center justify-content-center align-items-center">
                    {{ $depots->links() }}
                </div>
            @endif
            <div class="row mt-1" id="modification" style="display: none;">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                </div>
                <h1 class="h2"><input type="text" style="border:none;font-weight: bold;" class="form-control"
                        name="titreIntituleModif" disabled></h1>
                <div class="row d-flex text-center justify-content-center align-items-center">
                    <form id="formModif" class="row g-3" method="post">
                        @csrf
                        <input type="hidden" name="_method" value="put">
                        <input type="text" class="form-control" id="idModif" name="idModif" style="display:none">

                        <div class="mb-3 row">
                            <label for="inputPassword" class="col-sm-2 col-form-label h4">Intitule</label>
                            <div class="col-md-2 col-12">
                                <input type="text" class="form-control" id="intituleModif" name="intituleModif"
                                    readonly>
                            </div>
                            <label for="inputPassword" class="col-sm-2 col-form-label h4">Prefixe</label>
                            <div class="col-md-2 col-12">
                                <input type="text" class="form-control" id="prefixeModif" name="prefixeModif"
                                    readonly>
                            </div>
                            <label for="inputPassword" class="col-sm-2 col-form-label h4">Type</label>
                            <div class="col-md-2 col-12">
                                <input type="text" class="form-control" name="typeModif">
                            </div>
                            <label for="inputPassword" class="col-sm-2 col-form-label h4">Adresse</label>
                            <div class="col-md-2 col-12">
                                <input type="text" class="form-control" name="adresseModif">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning mb-3 "><i
                                    class="fas fa-save"></i>Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let editBtns = document.querySelectorAll('.edit-btn');
            editBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Récupérer les données de la carte sélectionnée
                    let id = this.getAttribute('data-id');
                    let intitule = this.getAttribute('data-intitule');
                    let type = this.getAttribute('data-type');
                    let adresse = this.getAttribute('data-adresse');
                    let prefixe = this.getAttribute('data-prefixe');

                    // Mettre à jour les champs du formulaire
                    document.querySelector('input[name="idModif"]').value = id;
                    document.querySelector('input[name="titreIntituleModif"]').value = "#" +
                        intitule;
                    document.querySelector('input[name="intituleModif"]').value = intitule;
                    document.querySelector('input[name="typeModif"]').value = type;
                    document.querySelector('input[name="adresseModif"]').value = adresse;
                    document.querySelector('input[name="prefixeModif"]').value = prefixe;
                    // Afficher la zone de modification
                    document.getElementById('modification').style.display = 'block';

                    // Faire défiler automatiquement jusqu'à la zone de modification
                    document.getElementById('modification').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    const formulaire = document.getElementById('formModif');
                    formulaire.action = `{{ route('modifier.depot', ['depot' => ':id']) }}`
                        .replace(':id', id);
                });
            });
        });

        // Fonction pour ouvrir le modal et stocker la valeur du dépôt
        function openModalWithDepot(depotIntitule) {
            // Stocker l'intitulé du dépôt dans un attribut de l'élément modal
            var modalElement = document.getElementById('modalCodeAccesDepot');
            modalElement.setAttribute('data-depot-intitule', depotIntitule);

            // Afficher le modal
            var modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

        document.getElementById('validerCodeAccesDepot').addEventListener('click', function() {
            var codeAcces = document.getElementById('codeAccesInputDepot').value;
            var modalElement = document.getElementById('modalCodeAccesDepot');
            var modal = bootstrap.Modal.getInstance(modalElement);
            var depotIntitule = modalElement.getAttribute('data-depot-intitule');

            // Créer une requête fetch pour vérifier le code d'accès
            fetch('/check-code-acces-depot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        code_acces: codeAcces,
                        depot_intitule: depotIntitule
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
                        document.getElementById('depotValue').value = depotIntitule;
                        document.getElementById('formDepot').submit();
                        modal.hide();
                    } else {
                        // Afficher une alerte avec SweetAlert en cas de code incorrect
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Code d\'accès incorrect.',
                            icon: 'error',
                            timer: 2000, // Durée en millisecondes (2 secondes)
                            showConfirmButton: false // Masque le bouton "OK"
                        });
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification du code d\'accès:', error);
                    alert('Une erreur est survenue. Veuillez réessayer.');
                });
        });


        function confirmDelete(depotId) {
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
                            // Utilisation de SweetAlert pour la confirmation de suppression
                            Swal.fire({
                                title: 'Êtes-vous sûr ?',
                                text: "Cette action est irréversible !",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Oui, supprimer',
                                cancelButtonText: 'Annuler'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    document.getElementById('form-' + depotId).submit();


                                }
                            });

                            modalCodeAcces.hide();
                        } else {
                            // Afficher une alerte avec SweetAlert en cas de code incorrect
                            Swal.fire({
                                title: 'Erreur',
                                text: 'Code d\'accès incorrect.',
                                icon: 'error',
                                timer: 2000, // Durée en millisecondes (2 secondes)
                                showConfirmButton: false // Masque le bouton "OK"
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la vérification du code d\'accès:', error);
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la vérification du code d\'accès.',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    });
            });
        }
    </script>
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
                            document.getElementById('parametre').submit();
                            modalCodeAcces.hide();
                        } else {
                            Swal.fire({
                                title: 'Erreur',
                                text: 'Code d\'accès incorrect.',
                                icon: 'error',
                                timer: 2000, // Durée en millisecondes (2 secondes)
                                showConfirmButton: false // Masque le bouton "OK"
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la vérification du code d\'accès:', error);
                    });
            });
            //
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Vérifier si un message de succès est défini dans la session
        @if (session('successModify'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('successModify') }}",
                icon: 'success',
                timer: 2000, // L'alerte disparaît après 2 secondes
                showConfirmButton: false
            });
        @endif
        @if (session('successDelete'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('successDelete') }}",
                icon: 'success',
                timer: 2000, // L'alerte disparaît après 2 secondes
                showConfirmButton: false
            });
        @endif
        @if (session('successUpdate'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('successUpdate') }}",
                icon: 'success',
                timer: 2000, // L'alerte disparaît après 2 secondes
                showConfirmButton: false
            });
        @endif
        @if (session('successDepot'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('successDepot') }}",
                icon: 'success',
                timer: 2000, // L'alerte disparaît après 2 secondes
                showConfirmButton: false
            });
        @endif
        @if (session('errorDelete'))
            Swal.fire({
                title: 'Erreur',
                text: "{{ session('errorDelete') }}",
                icon: 'error',
                timer: 2000, // L'alerte disparaît après 2 secondes
                showConfirmButton: false
            });
        @endif
    </script>
@endsection
