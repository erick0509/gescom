@extends("layouts.header")

@section("contenuPrincipale")
<section id="paiement-client" class="client py-4">
  <div class="container">
    <!-- Section Formulaire d'ajout -->
    <div class="row mt-5">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2>Avancements du client </h2>
            <p><strong>Intitulé:</strong> {{ $client->intituleClient }}</p>
            <p><strong>Contact:</strong> {{ $client->contactClient }}</p>
            <p><strong>Adresse:</strong> {{ $client->adresseClient }}</p>
      </div>
    </div>

    <h1 class="h4">Nouveau </h1>
    <div class="row d-flex text-center justify-content-center align-items-center">
      <div class="col-md-10">
        <!-- Formulaire de paiement du client -->
        <form action="{{ route('avancement.store', $client->id) }}" method="POST">
          @csrf
          <div class="row">
            <!-- Montant du paiement -->
            <div class="col-md-4 mb-3">
              <label for="somme" class="form-label">Montant du Paiement (en Ariary)</label>
              <input type="number" step="0.01" class="form-control @error('somme') is-invalid @enderror" id="somme" name="somme" placeholder="Entrez le montant" value="{{ old('somme') }}" required>
              @error('somme')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Mode de paiement (champ texte libre) -->
            <div class="col-md-4 mb-3">
              <label for="mode_de_payement" class="form-label">Mode de Paiement ( ou Description )</label>
              <input type="text" class="form-control @error('mode_de_payement') is-invalid @enderror" id="mode_de_payement" name="mode_de_payement" placeholder="Entrez le mode de paiement" value="{{ old('mode_de_payement') }}" required>
              @error('mode_de_payement')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <!-- Bouton de soumission -->
          <div class="row">
            <div class="col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Enregistrer le Paiement</button>
            </div>
          </div>

          <!-- Messages de succès ou d'erreur -->
          @if (session('success'))
          <div class="alert alert-success mt-3">
            {{ session('success') }}
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

    <!-- Section Affichage des paiements enregistrés -->
    <div class="row mt-2">
      <div class="col-md-12">
        <h4>Historique des Paiements</h4>
        <div class="col-md-12">
                <form action="{{ route('avancement.search', $client->id) }}" method="GET" class="d-flex gap-2 mb-3">
                    <!-- Date de début -->
                    <div class="form-group mb-0">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="date" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                    </div>

                    <!-- Date de fin -->
                    <div class="form-group mb-0">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="date" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                    </div>

                    <!-- Bouton de recherche -->
                    <div class="form-group mb-0 align-self-end">
                        <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        @if($paiements->isEmpty())
        <div class="alert alert-warning">
          Aucune transaction n'a été enregistrée pour ce client.
        </div>
        @else
        
        <!-- Tableau des paiements -->
        <table class="table table-striped table-hover mt-3 ">
            <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Montant</th>
              <th>Mode de Paiement</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($paiements as $paiement)
            <tr>
              <td>{{ $paiement->primaryKey }}
              @if($paiement->dejaUtilise==1)
                  <i class="fas fa-times-circle text-danger" title="Paiement déjà utilisé"></i>
              @else
                  <i class="fas fa-check-circle text-success" title="Paiement utilisable"></i>
              @endif
              </td>
              <td>{{ $paiement->created_at->format('d/m/Y') }}</td>
              <td>{{ number_format($paiement->somme, 2, ',', ' ') }}</td>
              <td>{{ ucfirst($paiement->mode) }}</td>
              <td>
                <!-- Bouton d'impression -->
                <form action="{{route('avancement.detail', ['id' => $paiement->id, 'page' => $paiements->currentPage()])}}" method="GET">
                  <button class="btn-details-facture-achats btn btn-warning btn-sm" ><i class="fas fa-info-circle"></i></button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $paiements->links() }}
        </div>
        
        @endif
      </div>
    </div>
  </div>
</section>
@endsection
