@extends('layouts.header')
@section('contenuPrincipale')
    <section id="article-liste" class="article py-4">
        <div class="container">
            <!-- Section Formulaire d'ajout -->
            <div class="row mt-5">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Valeur en Caisse : {{ number_format($caisse->montant, 2, ',', ' ') }} Ar</h2>
                </div>
            </div>
            <h1 class="h2">Nouvelle Opération #</h1>
            <div class="row d-flex text-center justify-content-center align-items-center">
                <div class="col-md-10">
                    <!-- Formulaire d'opération -->
                    <form action="{{ route('operation.store') }}" method="POST">
                        @csrf

                        <!-- Alignement en ligne -->
                        <div class="row">
                            <!-- Type d'opération (Débiter / Créditer) -->
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type d'Opération</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type"
                                    name="type" required>
                                    <option value="" disabled selected>Choisir le type</option>
                                    <option value="debiter">Entré en Caisse</option>
                                    <option value="crediter">Sortie en Caisse</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Montant de l'opération -->
                            <div class="col-md-4 mb-3">
                                <label for="montant" class="form-label">Montant (en Ariary)</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('montant') is-invalid @enderror" id="montant"
                                    name="montant" placeholder="Entrez le montant" value="{{ old('montant') }}" required>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Commentaire -->
                            <div class="col-md-4 mb-3">
                                <label for="commentaire" class="form-label">Commentaire</label>
                                <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire"
                                    rows="1">{{ old('commentaire') }}</textarea>
                                @error('commentaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">Enregistrer l'Opération</button>
                            </div>
                        </div>
                        <!-- Affichage des messages de succès -->
                        @if (session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-warning mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <!-- Section Affichage des opérations enregistrées -->
            <div class="row mt-2">
                <div class="col-md-12">
                    <h2>Historique des Opérations</h2>
                    <!-- Formulaire de recherche par date -->
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <form action="{{ route('operation.search') }}" method="GET" class="d-flex gap-2 mb-3">
                                <!-- Date de début -->
                                <div class="form-group mb-0">
                                    <label for="start_date" class="form-label">Date de début</label>
                                    <input type="date" id="start_date" name="start_date"
                                        class="form-control form-control-sm" value="{{ request('start_date') }}">
                                </div>

                                <!-- Date de fin -->
                                <div class="form-group mb-0">
                                    <label for="end_date" class="form-label">Date de fin</label>
                                    <input type="date" id="end_date" name="end_date"
                                        class="form-control form-control-sm" value="{{ request('end_date') }}">
                                </div>

                                <!-- Bouton de recherche -->
                                <div class="form-group mb-0 align-self-end">
                                    <button type="submit" class="btn btn-warning btn-sm"><i
                                            class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if ($operations->isEmpty())
                        <div class="alert alert-warning">
                            Aucune opération n'a été enregistrée.
                        </div>
                    @else
                        <!-- Tableau des opérations -->
                        <table class="table table-striped table-hover mt-3 ">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Type d'Opération</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Commentaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ ucfirst($operation->type) }}</td>
                                        <td>{{ Carbon\Carbon::parse($operation->date_operation)->format('d/m/Y') }}</td>
                                        <td>{{ number_format($operation->montant, 2, ',', ' ') }}</td>
                                        <td>{{ $operation->commentaire }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            {{ $operations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
