@extends("layouts.header")
    @section("contenuPrincipale")
    <section  id="article-liste" class="article py-4 ">
      <div class="container">    
            <div class="row mt-5">
              <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Commande d'un client</h2>
                <form class="mt-2 col-md-5 col-sm-6" method="post" action="{{ route('client.store') }}">
                    @csrf
                    <label class="form-label h6">Nouveau Client</label>
                    
                    <!-- Champ pour la désignation -->
                    <div class="input-group mb-3">
                        <input class="form-control" name="intituleClient" type="search" placeholder="Intitule" aria-label="Search">
                        <input class="form-control" name="contactClient" type="text" placeholder="Contact" aria-label="Unité">
                        <input class="form-control" name="adresseClient" type="text" placeholder="Adresse" aria-label="Quantité par Pack">
                        <button class="btn btn-success" type="submit"><i class="fas fa-plus"></i> Creer</button>
                    </div>
                    
                </form>
              </div>    
            </div>
            @if(session()->has("success"))
                <div class="alert alert-success">
                    {{session()->get('success')}}
                </div>
            @endif
            <h1 class="h2">Nouvelle commande #</h1>
              <div class="row d-flex text-center justify-content-center align-items-center">
                  <form id="formAjouterFacture" class="row g-3">
              
                      <!-- Sélection du client avec un dropdown (select) -->
                      <div class="mb-3 row">
                          <label for="clientSelect" class="col-md-1 col-sm-2 col-form-label h4">Client:</label>
                          <div class="col-md-2 col-12">
                            <input type="text" class="form-control form-control-sm readonly-input" id="searchClient" placeholder="Saisir le nom du client" >
                          </div>
                          <div class="col-md-2 col-12">
                              <select class="form-select form-select-sm readonly-input" id="clientSelect" required>
                                  <option value="" disabled selected>Sélectionner un client</option>
                                  @foreach($clients as $client)
                                      <option value="{{ $client->id }}" data-nom="{{ $client->intituleClient }}" 
                                      data-adresse="{{ $client->adresseClient }}" 
                                      data-solde="{{ $client->solde }}" 
                                      data-contact="{{ $client->contactClient }}">{{ $client->intituleClient }}</option>
                                  @endforeach
                              </select>
                          </div>
                      </div>

                      <!-- Affichage des informations du client (readonly) -->
                      <div class="mb-3 row">
                          <label for="nomClient" class="col-md-1 col-sm-2 col-form-label h4">Nom:</label>
                          <div class="col-md-2 col-12">
                              <input type="text" class="form-control form-control-sm" id="nomClient" readonly required>
                          </div>

                          <label for="contactClient" class="col-md-1 col-sm-2 col-form-label h4">Contact:</label>
                          <div class="col-md-2 col-12">
                              <input type="text" class="form-control form-control-sm" id="contactClient" readonly>
                          </div>

                          <label for="contactClient" class="col-md-1 col-sm-2 col-form-label h4">Adresse:</label>
                          <div class="col-md-2 col-12">
                              <input type="text" class="form-control form-control-sm" id="adresseClient" readonly>
                          </div>
                          <label for="contactClient" class="col-md-1 col-sm-2 col-form-label h4">Creance N°:</label>
                          <div class="col-md-2 col-12">
                            <select class="form-select form-select-sm" id="paiementsClient" readonly>
                                <option value="" disabled selected>Sélectionner un creance</option>
                                <!-- Les paiements seront insérés ici dynamiquement -->
                            </select>
                          </div>
                          
                            <label for="montantCreance"  class="col-md-1 col-sm-2 col-form-label h4" >Montant de la créance:</label>
                            <div class="col-md-2 col-12 mt-2">
                                <input type="text" id="montantCreance" class="form-control form-control-sm" value="0" readonly>
                            </div>
                            <label for="montantCreance"  class="col-md-1 col-sm-2 col-form-label h4" >Description:</label>
                            <div class="col-md-2 col-12 mt-2" style="font-size:10px;">
                                <textarea id="description" class="form-control" rows="3" readonly></textarea>
                            </div>
                            <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4" >Date Echeance:</label>
                            <div class="col-md-2 col-12">
                                <input type="date" class="form-control readonly-input" id="date">
                            </div>
                      </div>

                      <!-- Bouton pour ajouter des lignes de commande -->
                      <div class="d-flex justify-content-center">
                          <button type="button" id="btnAjouter" class="btn btn-primary mb-3 me-2">Lignes de commande</button>
                      </div>
                  </form>
              </div>

          <div id="ajouterDesArticles" style="display:none;" >
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            </div> 
            <h1 class="h2">les articles pour le commande #</h1>
            <div class="col-12 mb-1">
                <input id="desi"class="form-control input-sm" type="search" placeholder="..." aria-label="Search" readonly>
            </div>
            <div class="row d-flex text-center justify-content-center align-items-center">
                <form class="row g-3" id="formAjouterArticle">
                <div class="mb-3 row">
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4 mt-3">Article:</label> 
                      <div class="col-md-2 col-12 mt-3">
                        <div class="input-group">
                          <div class="col-6">
                            <input id="designation"class="form-control" type="search" placeholder="Designation..." aria-label="Search">
                          </div>
                          <div class="col-6">
                          <select id="selectDesignation" class="form-select w-100" aria-label="ListeArticles" name="articles">
                            <option value="0" selected>...</option>
                            @foreach ($articles as $article)
                                <option value="{{ $article->id }}">{{ $article->designation }}</option>
                            @endforeach
                          </select>
                          </div>
                        </div>
                      </div>
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4 mt-3">Stock Réel:</label>
                      <div class="col-md-2 col-12 mt-3">
                        <input type="number" class="form-control readonly-input-article" name="stock" id="stock" readonly>
                      </div>
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4 mt-3">Quantite:</label>
                      <div class="col-md-2 col-12 mt-3">
                        <input type="number" class="form-control readonly-input-article" name="quantite" readonly required>
                      </div>
                      <div class="col-md-2 col-12 mt-3 ">
                          <button type="button" id="btnAjouterArticle" class="btn btn-primary mb-3 me-2 form-control" disabled>Ajouter</button> 
                      </div>
                    </div>
                </form>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">     
            </div>
            <div class="row mt-1" >
                <h1 class="h2">Commande #</h1>
                <div class="row d-flex text-center justify-content-center align-items-center ">
                  <div class="col-md-12">
                  <table class="table" id="tableauVente">
                    <thead>
                      <tr>
                        <th scope="col">Designation</th>
                        <th scope="col">Quantite</th>
                        <th scope="col">P.U</th>
                        <th scope="col">Remise</th>
                        <th scope="col">Montant</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>                     
                    </tbody>
                  </table>
                  </div>
                </div>  
                <div id="remiseSection" class="d-flex flex-column align-items-start mt-3">
                    <h3 id="totalMontantNet" class="mt-2"></h3>
                    <div class="mb-2">
                        <label for="remiseTotalMontant" class="h4">Remise :</label>
                        <input type="number" id="remiseTotalMontant" class="form-control input-sm readonly-input-article mt-1" name="remiseTotalMontant" placeholder="Montant de la remise" disabled>
                    </div>
                    <h3 id="totalMontant" class="mt-2"></h3>
                </div>
              </div> 
            <div class="d-flex justify-content-start">
              <button type="button" id="btnAnnuler"class="btn btn-warning mb-3 me-2" style="display:none"><i class="fas fa-times-circle"></i> Annuler</button>
              <button type="submit" id="btnValider" class="btn btn-success mb-3" ><i class="fas fa-check-circle"></i> Valider</button>
            </div>
          </div>
            
        </div>
          
    </section>
    <!-- Bouton ajouter des articles-->
    <script>     
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

                      document.addEventListener('DOMContentLoaded', function() {
                      var btnAjouter = document.getElementById('btnAjouter');
                      var btnAnnuler = document.getElementById('btnAnnuler');
                      var inputs = document.querySelectorAll('.readonly-input');
                      var ajouterDesArticles = document.getElementById('ajouterDesArticles');
                      var tableauDepots = document.getElementById('tableauVente');
                      function calculerTotalMontant() {
                            const rows = document.querySelectorAll('#tableauVente tbody tr');
                            let totalMontant = 0;

                            // Calcul du total net initial
                            rows.forEach(row => {
                                const cellValue = parseFloat(row.cells[5].textContent.trim());
                                if (!isNaN(cellValue)) {
                                    totalMontant += cellValue;
                                }
                            });
                            document.getElementById('remiseTotalMontant').value = '';
                            // Mettre à jour l'affichage des totaux
                            document.getElementById('totalMontantNet').textContent = totalMontant > 0 
                                ? 'Total Net: ' + formatNumber(totalMontant.toFixed(2)) + ' Ar' 
                                : '';
                            document.getElementById('totalMontant').textContent = totalMontant > 0 
                                ? 'Total: ' + formatNumber(totalMontant.toFixed(2)) + ' Ar' 
                                : '';

                            // Activer le champ de remise uniquement si le total est supérieur à 0
                            document.getElementById('remiseTotalMontant').disabled = totalMontant <= 0;

                            // Sauvegarder le total initial pour calculer les remises
                            document.getElementById('remiseTotalMontant').dataset.totalMontantInitial = totalMontant;
                        }
                    tableauDepots.addEventListener('click', function(event) {
                      if (event.target.classList.contains('delete-btn')) {
                          // Récupérer la ligne parente de l'élément de bouton supprimé
                          var row = event.target.closest('tr');
                          if (row) {
                              // Récupérer le montant de la ligne à supprimer
                              var montantRow = parseFloat(row.cells[3].textContent.trim());
                              // Soustraire le montant de la ligne supprimée du total
                              // Supprimer la ligne du tableau
                              row.remove();
                          }
                          
                      }
                      calculerTotalMontant();
                    });
                    btnAjouter.addEventListener('click', function() {
                            var form = document.getElementById('formAjouterFacture');
                            var dateInput = document.getElementById('date'); // L'ID de l'input date
                            var inputDate = new Date(dateInput.value); // Convertir la valeur de l'input en objet Date
                            var currentDate = new Date(); // Date système (date actuelle)
                            inputDate.setHours(0, 0, 0, 0);
                            currentDate.setHours(0, 0, 0, 0);
                            // Vérifier si la date n'est pas vide et est supérieure ou égale à la date système
                            if (form.checkValidity() && inputDate >= currentDate) {
                                // Si la validation des champs réussit et que la date est valide
                                ajouterDesArticles.style.display = 'block';
                                ajouterDesArticles.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                                inputs.forEach(function(input) {
                                    input.readOnly = true;
                                });
                                btnAjouter.style.display = 'none';
                                btnAnnuler.style.display = 'inline-block';
                                btnAnnuler.disabled = false;
                                document.getElementById('clientSelect').disabled = true;
                                document.getElementById('paiementsClient').disabled = true;
                            } else if (inputDate < currentDate) {
                                // Si la date est invalide (inférieure à la date système)
                                alert('Veuillez entrer une date valide (aujourd\'hui ou une date future).');
                            } else {
                                // Si la validation échoue
                                alert('Veuillez remplir tous les champs obligatoires.');
                            }                
                        });


                      btnAnnuler.addEventListener('click', function() {
                        
                        inputs.forEach(function(input) {
                            input.readOnly = false;
                        });
                        var totalElement = document.getElementById('totalMontant');
                        var totalElementNet = document.getElementById('totalMontantNet');
                        var rowsToDelete = tableauDepots.querySelectorAll('.row-to-delete');
                        rowsToDelete.forEach(function(row) {
                        row.remove();
                        });
                        totalElement.textContent ="";
                        totalElementNet.textContent ="";
                        ajouterDesArticles.style.display = 'none';
                        btnAjouter.style.display = 'inline-block';
                        btnAnnuler.style.display = 'none';
                        btnAnnuler.disabled = true;

                        document.getElementById('remiseTotalMontant').value='';
                        document.getElementById('searchClient').value='';
                        document.getElementById('clientSelect').selectedIndex = 0;
                        document.getElementById('nomClient').value = '';
                        document.getElementById('contactClient').value = '';
                        document.getElementById('adresseClient').value = '';
                        document.getElementById('date').value = '';
                        document.getElementById('clientSelect').disabled = false;
                        document.getElementById('paiementsClient').disabled = false;
                      });
                      
                    });
                    ////Validation des champs
             
    </script>
    <!--ajout des article-->
    <script>
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }
        // Écouteur d'événement sur le bouton "Ajouter"
        document.getElementById('btnAjouterArticle').addEventListener('click', function() {
            // Récupérer l'ID de l'article et la quantité depuis les éléments du formulaire
            var articleId = document.getElementById('selectDesignation').value;
            var quantite = document.getElementsByName('quantite')[0].value;
            var btnAjouter = document.getElementById('btnAjouterArticle');
            var quantiteInput = document.querySelector('input[name="quantite"]');
            // Faire la requête AJAX
            if((quantite!==null || quantite!==0) && quantite>0)
            {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/getPrixUnitaire/' + articleId + '/' + quantite, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            // Mettre à jour le tableau avec le prix unitaire récupéré
                            if (response.prixUnitaire === 0 || response.prixUnitaire === null) {
                                alert('Le prix unitaire est Vide ou egale a zero.');
                            } else {
                                // Mettre à jour le tableau avec le prix unitaire récupéré
                                var quantitePack = response.quantitePack;
                                var unite = response.unite;
                                var quantiteAffichee;
                                var quantiteStock = response.quantiteStock;
                                if (quantiteStock == null || quantiteStock ==0) {
                                    alert('l\'Article n\'est pas disponnible');
                                }
                                else if (quantiteStock !== null && quantiteStock < quantite) {
                                    alert('La quantité demandée dépasse la quantité en stock.');
                                } else if (quantiteStock !== null && quantiteStock >= quantite){
                                // Calculer la quantité à afficher en cartons et pièces
                                    if (quantitePack === null || quantitePack === 0) {
                                        quantiteAffichee ='<b>'+ quantite +' '+(unite || ' ')+'</b>';
                                    } else {
                                        // Calculer la quantité à afficher en cartons et pièces
                                        var cartons = Math.floor(quantite / quantitePack);
                                        var pieces = quantite % quantitePack;

                                        if (cartons > 0 && pieces > 0) {
                                            quantiteAffichee = '<b>'+cartons + ' Ct </b> / <b>' + pieces +' '+(unite || ' ')+'</b>';
                                        } else if (cartons > 0 && pieces === 0) {
                                            quantiteAffichee = '<b>'+cartons + ' Ct </b>';
                                        } else {
                                                quantiteAffichee = '<b>'+pieces + ' '+(unite || ' ')+'</b>';
                                        }
                                    }

                                    // Mettre à jour le tableau avec les données récupérées
                                    var newRow = '<tr class="row-to-delete">' +
                                        '<td>' + response.designation + '</td>' +
                                        '<td style="display:none;">' + quantite + '</td>'+
                                        '<td>' + quantiteAffichee + '</td>' +
                                        '<td><input style="border:none; text-align:center;" type="number" class="prix-unitaire-input" value="' + response.prixUnitaire + '"></td>' +
                                        '<td><input style="border:none; text-align:center;" type="number" class="remise-input"></td>' +
                                        '<td class="montant-total">' + (quantite * response.prixUnitaire).toFixed(2) + '</td>' +
                                        '<td><button type="button" class="btn btn-danger delete-btn btn-sm">Supprimer</button></td>' +
                                        '</tr>';


                                    document.querySelector('#tableauVente tbody').insertAdjacentHTML('beforeend', newRow);
                                    // Calculer le total du montant
                                    calculerTotalMontant();
                                    var form = document.getElementById('formAjouterArticle');
                                    form.reset(); 
                                    quantiteInput.readOnly = true;
                                    btnAjouter.disabled = true;
                                    //
                                    document.querySelectorAll('.prix-unitaire-input').forEach(function(input) {
                                        input.addEventListener('input', function() {
                                            recalculerMontantLigne(this);
                                        });
                                    });
                                    document.querySelectorAll('.remise-input').forEach(function(input) {
                                        input.addEventListener('input', function() {
                                            recalculerMontantLigneAvecRemise(this);
                                        });
                                    });
                                    function recalculerMontantLigne(input) {
                                        var newPrice = parseFloat(input.value);
                                        var quantite = parseFloat(input.closest('tr').querySelector('td:nth-child(2)').innerText);
                                        var remise = input.closest('tr').querySelector('.remise-input').value;
                                        var montantTotalCell = input.closest('tr').querySelector('.montant-total');
                                        var newTotal = ((newPrice-remise) * quantite).toFixed(2);
                                        montantTotalCell.textContent = newTotal;
                                        calculerTotalMontant();
                                    }      
                                    function recalculerMontantLigneAvecRemise(input) {
                                      var newRemise = parseFloat(input.value);
                                      if (isNaN(newRemise)) {
                                          newRemise = 0; // Si newRemise est NaN, attribuez-lui la valeur zéro
                                      }
                                      var row = input.closest('tr');
                                        var quantite = parseFloat(input.closest('tr').querySelector('td:nth-child(2)').innerText);
                                        var prixUnitaire = parseFloat(row.querySelector('.prix-unitaire-input').value);
                                        var montantTotalCell = input.closest('tr').querySelector('.montant-total');
                                        var newTotal = ((prixUnitaire-newRemise) * quantite).toFixed(2);
                                        montantTotalCell.textContent = newTotal;
                                        calculerTotalMontant();
                                    }           
                                    //
                                    

                                }
                            }
                        } else {
                            console.error('Erreur lors de la récupération du prix unitaire.');
                        }
                    }
                };
                xhr.send();
            }
            else{
                alert('saisie incorrecte!');
            }
            
        });
                    
        // Fonction pour calculer le total du montant dans le tableau
        /*
        function calculerTotalMontant() {
            var rows = document.querySelectorAll('#tableauVente tbody tr');
            var totalMontant = 0;
            rows.forEach(function(row) {
                totalMontant += parseFloat(row.cells[5].textContent);
            });
            document.getElementById('totalMontant').textContent = 'Total: ' + formatNumber(totalMontant.toFixed(2))+' Ar';
        }
        */
       function calculerTotalMontant() {
            const rows = document.querySelectorAll('#tableauVente tbody tr');
            let totalMontant = 0;

            // Calcul du total net initial
            rows.forEach(row => {
                const cellValue = parseFloat(row.cells[5].textContent.trim());
                if (!isNaN(cellValue)) {
                    totalMontant += cellValue;
                }
            });
            document.getElementById('remiseTotalMontant').value = '';
            // Mettre à jour l'affichage des totaux
            document.getElementById('totalMontantNet').textContent = totalMontant > 0 
                ? 'Total Net: ' + formatNumber(totalMontant.toFixed(2)) + ' Ar' 
                : '';
            document.getElementById('totalMontant').textContent = totalMontant > 0 
                ? 'Total: ' + formatNumber(totalMontant.toFixed(2)) + ' Ar' 
                : '';

            // Activer le champ de remise uniquement si le total est supérieur à 0
            document.getElementById('remiseTotalMontant').disabled = totalMontant <= 0;

            // Sauvegarder le total initial pour calculer les remises
            document.getElementById('remiseTotalMontant').dataset.totalMontantInitial = totalMontant;
        }


                

        // Fonction d'affichage du nombre au format local avec séparateur de milliers
        function formatNumber(number) {
            return new Intl.NumberFormat().format(number);
        }

        
    </script>
    <!-- remise input-->
    <script>
        document.getElementById('remiseTotalMontant').addEventListener('input', () => {
            const totalMontantInitial = parseFloat(document.getElementById('remiseTotalMontant').dataset.totalMontantInitial) || 0;
            const remise = parseFloat(document.getElementById('remiseTotalMontant').value) || 0;

            // Calculer le montant avec la remise et mettre à jour l'affichage
            const totalAvecRemise = totalMontantInitial - remise;
            document.getElementById('totalMontant').textContent = remise > 0 
                ? 'Total: ' + formatNumber(totalAvecRemise.toFixed(2)) + ' Ar' 
                : 'Total: ' + formatNumber(totalMontantInitial.toFixed(2)) + ' Ar';
        });
    </script>
    <!-- inputs designation-->
    <script>
            document.addEventListener('DOMContentLoaded', function() {
                var btnAjouter = document.getElementById('btnAjouterArticle');
                var designationInput= document.getElementById('designation');
                var selectDesignation = document.getElementById('selectDesignation');
                var quantiteInput = document.querySelector('input[name="quantite"]');
                const inputDesi = document.getElementById('desi');
                function updateReadonlyState() {
                    if (selectDesignation.value !== selectDesignation.options[0].value) {
                        var designation = selectDesignation.options[selectDesignation.selectedIndex].text;
                        var encodedDesignation = encodeURIComponent(designation);
                        fetch(`/get-article-stock?designation=${encodedDesignation}`)
                          .then(response => response.json())
                          .then(data => {
                              if (data.quantiteDepot !== undefined) {
                                  //document.getElementById('prixAchat').value = data.prixMoyenAchat; // Met à jour le champ "Prix d'achat"
                                  document.getElementById('stock').value = data.quantiteDepot; 
                              } else {
                                  console.error('Article not found');
                              }
                          })
                          .catch(error => console.error('Error:', error));
                        quantiteInput.readOnly = false;
                        btnAjouter.disabled = false;
                    } else {
                        quantiteInput.readOnly = true;
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
                });
                updateReadonlyState();
            });
    </script>
    <!-- JSON-->
    <script>
            document.addEventListener('DOMContentLoaded', function() {
            var btnValider = document.getElementById('btnValider'); // Bouton de validation du formulaire
            var tableauAchat = document.getElementById('tableauVente'); // Tableau dynamique

            btnValider.addEventListener('click', function() {
              var rows = tableauAchat.querySelectorAll('tbody tr');
              if (rows.length === 0) {
                alert("La ligne de commande est vide. Veuillez ajouter des éléments avant de valider.");
              } else {
              var confirmation = confirm("Êtes-vous sûr de vouloir valider ?");
                if (confirmation) {
                  var tableData = []; // Tableau pour stocker les données du tableau
                  // Capturer les données de la facture
                  idClient=document.getElementById('clientSelect').value;
                  remiseT=document.getElementById('remiseTotalMontant').value;
                  p=document.getElementById('paiementsClient');
                  pkPaiement=p.options[p.selectedIndex].getAttribute('data-idPaiement');
                  
                  dateEcheance=document.getElementById('date').value;
                  var montantTotal=0;
                  //ajouter d'abord la facture
                  var rows = tableauAchat.querySelectorAll('tbody tr');
                  rows.forEach(function(row) {
                    var cells = row.querySelectorAll('td');
                      montantTotal+=parseFloat(cells[5].textContent);
                  });
                  if(remiseT===''){
                    remiseT=0;
                  }
                  montantTotal=montantTotal-remiseT;
                  tableData.push({
                      idClient: idClient, // Ajout de l'idClient
                      montantTotals: montantTotal,
                      dateEcheance: dateEcheance,
                      pkPaiement:pkPaiement,
                      remiseT:remiseT
                  });
                  // Parcourir chaque ligne du tableau
                  rows.forEach(function(row) {
                    var rowData = {}; // Objet pour stocker les données de chaque ligne
                    var cells = row.querySelectorAll('td'); // Cellules de la ligne

                    // Capturer les données de chaque cellule
                    rowData.designation = cells[0].textContent;
                    rowData.quantite = cells[1].textContent;
                    rowData.quantiteAffichee = cells[2].textContent;
                    remise=cells[4].querySelector('input').value;
                    rowData.prix = cells[3].querySelector('input').value-remise; 
                    
                    if(cells[4].querySelector('input').value ===''){
                      rowData.remise=0;
                    }
                    else{
                      rowData.remise = cells[4].querySelector('input').value;
                    }                  
                    rowData.montant = cells[5].textContent;
                    // Ajouter les données de la ligne au tableau
                    tableData.push(rowData);
                  });
                  sendDataToServer(tableData);
                }
              }
            });

          // Fonction pour envoyer les données au serveur via Ajax
          function sendDataToServer(data) {
                    console.log(JSON.stringify(data));
                    fetch('/documentVente/creerVente', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                    })
                .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la requête fetch');
                }
                return response.json();
                })
                .then(data => {
                alert(data.message);
                let factureId = data.id; // Récupérer l'ID de la facture depuis la réponse
                let page = data.page; // Récupérer la page depuis la réponse, ici elle sera 0

                // Redirection vers la nouvelle route avec l'ID de la facture et la page
                window.location.href = "/caisse/" + factureId + "/" + page;
                //window.location = "{{ route('documentvente') }}";
                // Traitez la réponse du serveur si nécessaire
                })
                .catch(error => {
                console.error('Erreur lors de l\'envoi des données:', error.message);
                // Traitez les erreurs d'envoi si nécessaire
                });

            }

     });

    </script>
    <script>
      const selectDesignation = document.getElementById('selectDesignation');
      const inputDesi = document.getElementById('desi');

      // Ajouter un écouteur d'événements sur le changement de valeur dans le select
      selectDesignation.addEventListener('change', function() {
          // Mettre à jour la valeur de l'input avec la valeur sélectionnée dans le select
          inputDesi.value = selectDesignation.options[selectDesignation.selectedIndex].text;
      });
    </script>

<script>
    document.getElementById('searchClient').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const clientSelect = document.getElementById('clientSelect');
        const options = clientSelect.querySelectorAll('option');
        let firstMatch = null;

        options.forEach(option => {
            const clientName = option.textContent.toLowerCase();
            if (clientName.includes(searchValue)) {
                option.style.display = 'block'; // Affiche les options correspondantes
                if (!firstMatch) {
                    firstMatch = option;
                }
            }
        });

        // Sélection automatique du premier résultat correspondant
        if (firstMatch) {
            clientSelect.value = firstMatch.value;
            updateClientInfo(firstMatch);
        } else {
            clientSelect.value = '';
            updateClientInfo(null);
        }
    });

    // Met à jour les champs readonly lorsque l'option du client change
    document.getElementById('clientSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        updateClientInfo(selectedOption);
    });

    function updateClientInfo(option) {
        var paiementSelect = document.getElementById('paiementsClient');
          paiementSelect.innerHTML = '<option value="" disabled selected> Sélectionner un creance </option>';
        if (option) {
          document.getElementById('nomClient').value = option.getAttribute('data-nom');
          document.getElementById('contactClient').value = option.getAttribute('data-contact');
          document.getElementById('adresseClient').value = option.getAttribute('data-adresse');
          

        fetch('/getCreances/' + option.value)
        .then(response => response.json())
        .then(data => {
            // Ajouter les paiements au select
            data.paiements.forEach(paiement => {
                var option = document.createElement('option');
                option.value = paiement.id; // ou un autre champ que vous souhaitez associer
                option.text =  paiement.primaryKey;
                option.setAttribute('data-idPaiement', paiement.id); 
                option.setAttribute('data-montant', paiement.somme); 
                option.setAttribute('data-mode', paiement.mode);  
                paiementSelect.appendChild(option);
                
            });
        })
        .catch(error => console.error('Erreur:', error));
          
        } else {
          document.getElementById('nomClient').value = '';
          document.getElementById('contactClient').value = '';
          document.getElementById('adresseClient').value = '';
          document.getElementById('montantCreance').value ='';
            document.getElementById('description').value ='';
        
        }
    }
</script>
<script>
    document.getElementById('clientSelect').addEventListener('change', function() {
        const selectedClient = this.options[this.selectedIndex];
        document.getElementById('nomClient').value = selectedClient.getAttribute('data-nom');
        document.getElementById('contactClient').value = selectedClient.getAttribute('data-contact');
        document.getElementById('adresseClient').value = selectedClient.getAttribute('data-adresse');
        
       
    });
</script>

<!--choix creance -->
<script>
    document.getElementById('paiementsClient').addEventListener('change', function() {
    var montantCreance = this.options[this.selectedIndex].getAttribute('data-montant');
    var description = this.options[this.selectedIndex].getAttribute('data-mode');
    
    // Si le montant est nul ou non défini, afficher 0
    if (!montantCreance) {
        montantCreance = 0;
    }
    if (!description) {
        description = '';
    }

    // Mettre à jour le champ montant
    document.getElementById('montantCreance').value = montantCreance;
    document.getElementById('description').value = description;
});
</script>

    @endsection