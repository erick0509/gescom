@extends("layouts.header")
@section("contenuPrincipale")
<section  id="article-liste" class="article py-4 ">
  <div class="container">  
        <div class="row mt-5">
          <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h2>Caisse</h2>
          </div> 
          <div class="border border-secondary rounded p-3 position-relative">
                <div class="position-absolute top-0 start-0 m-2">
                    <p class="text-muted fs-6">{{ now()->format('d/m/Y') }}</p>
                </div>
                <h4 class="mb-0 mt-3">Montant en caisse: {{ number_format($caisse->montant, 1, ',', ' ') }} Ar</h4>
          </div>  
        </div>
        <div class="mt-2 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Commandes en attente</h4>
            <div class="col-md-1 col-2 mt-2">
                <a href="{{ route('caisse') }}" class="btn btn-secondary btn-sm col-12 mb-1"><i class="fas fa-sync-alt"></i></a>
            </div>
        </div>
        <div class="row d-flex text-center justify-content-center align-items-center">
              <form action="{{ route('rechercher.documentAttente') }}" class="mt-1 col-md-4 col-sm-6" method="GET">
              @csrf
                <div class="input-group ">
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon3">N°:</span>
                  </div>
                  <input value="{{ $zoneChercher ?? old('zoneChercher') }}" id="zoneChercher" name="zoneChercher" class="form-control" type="search" placeholder="Recherche..." aria-label="Search">
                  <button id="btnSearch" class="btn btn-warning" type="submit"><i class="fas fa-search"></i></button>
                </div>
              </form>
          </div>
        <div id="contenu-principal">
          @include('partials.liste_ventes_depot')
        </div>
  </div>
</section>
<script src="{{asset('js/jquery-3.6.0.min.js')}}">
</script>
@endsection

   