<!-- partials/liste_ventes_depot.blade.php -->

@if($ventesDepot->isEmpty() && $ventesDepot->currentPage() === 1)
    <div class="alert alert-danger text-center mt-2" role="alert">
        <p>Il n'y a pas de commande en attente!</p>.
    </div>
@else
<div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">
          <table class="table table-striped table-hover mt-3 ">
            <thead class="table-light">
              <tr>
                <th scope="col">N° Pièce</th>
                <th scope="col">Date Pièce</th>
                <th scope="col">Client</th>
                <th scope="col">Contact</th>
                <th scope="col">Total TTC</th>
                <th scope="col">Solde Dû</th>
                <th scope="col" class="texte-court">Actions</th>
              </tr>
            </thead>
            <tbody>
                @foreach($ventesDepot as $facture)
                <tr style="font-size: 13px;">
                  <td><b> {{$facture->primaryKey}}</b></td>
                  <td ><b>{{\Carbon\Carbon::parse($facture->dateVente)->format('d/m/Y')}}</b></td>
                  <td ><b>{{$facture->client->intituleClient}}</b></td>
                  <td ><b>{{$facture->client->contactClient}}</b></td>
                  <td  class="no-wrap"><b>{{number_format($facture->montantTotal,1,',',' ')}}</b></td>
                  <td  class="no-wrap"><b>{{number_format($facture->montantTotal-$facture->sommePayee,1,',',' ')}}</b></td>
                  <td class="texte-court">
                    <div class="d-flex justify-content-center">
                      <form id="form-{{$facture->id}}" action="{{ route('commande.details',['id' => $facture->id, 'page' => $ventesDepot->currentPage()]) }}" method="get">
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
        <b>{{$ventesDepot->currentPage()}}</b>
    </div>
    <div class="pagination row mt-1 d-flex text-center justify-content-start align-items-center">
        {{$ventesDepot->links()}}
    </div>
@endif
