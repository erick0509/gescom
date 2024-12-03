@extends("layouts.header")
    @section("contenuPrincipale")
    <section  id="article-liste" class="article py-4 ">
      <div class="container">    
            <div class="row mt-5">
              <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Effectuer un transfert</h2>
              </div>    
            </div>
            <h1 class="h2">Nouvelle Transfert #</h1>
            <div class="row d-flex text-center justify-content-center align-items-center">
                <form id="formAjouterFacture" class="row g-3"  >
                    <div class="mb-3 row">
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4">Depot Source:</label>
                      <div class="col-md-2 col-12">
                        <input type="text" class="form-control readonly-input" id="depotSource" value="{{session('depotValue')}}" disabled>
                      </div>
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4">Depot Destinataire:</label>
                      <div class="col-md-2 col-12">
                        <select id="depotDestinataire" class="form-select w-100" aria-label="ListeArticles" name="depotDestinataire" required>         
                          @foreach ($depots as $depot)
                            <option value="{{ $depot->id }}">{{ $depot->intitule }}</option>
                          @endforeach
                        </select>
                      </div>
                      <label for="inputPassword" class="col-md-2 col-sm-2 col-form-label h4">Commentaire:</label>
                      <div class="col-md-2 col-12">
                        <input type="textarea" class="form-control readonly-input" id="commentaire">
                      </div>
                    </div>
                    <div class="d-flex justify-content-center">
                      <button type="button" id="btnAjouter"class="btn btn-primary mb-3 me-2" >lignes de transfert</button>                 
                    </div>
                  </form>
            </div>
          <div id="ajouterDesArticles" style="display:none;" >
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            </div> 
            <h1 class="h2">Les articles pour le Transfert #</h1>
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
                <h1 class="h2">Transfert #</h1>
                <div class="row d-flex text-center justify-content-center align-items-center ">
                  <div class="col-md-12">
                  <table class="table" id="tableauVente">
                    <thead>
                      <tr>
                        <th scope="col">Designation</th>
                        <th scope="col">Quantite</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>                     
                    </tbody>
                  </table>
                  </div>
                </div>
                <div class="d-flex justify-content-start">
                    <h3 id="totalMontant" class="me-3"></h3>
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
                      var depotDestinataires = document.getElementById('depotDestinataire');
                      //var depotDestinataire = depotDestinataires.options[depotDestinataires.selectedIndex].text;
                      
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
                      
                    });
                      btnAjouter.addEventListener('click', function() {
                        var form = document.getElementById('formAjouterFacture');
                        if (form.checkValidity()) {
                        // Si la validation des champs réussit, exécutez le code pour ajouter les articles
                        ajouterDesArticles.style.display='block';
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
                        depotDestinataires.disabled=true;
                        } else {
                        // Si la validation échoue, affichez un message d'erreur ou effectuez une autre action
                          alert('Veuillez remplir tous les champs obligatoires.');
                        }
                      
                      });

                      btnAnnuler.addEventListener('click', function() {
                      inputs.forEach(function(input) {
                        input.readOnly = false;
                      });
                      var totalElement = document.getElementById('totalMontant');
                      var rowsToDelete = tableauDepots.querySelectorAll('.row-to-delete');
                      rowsToDelete.forEach(function(row) {
                      row.remove();
                      });
                      totalElement.textContent ="";
                      ajouterDesArticles.style.display = 'none';
                      btnAjouter.style.display = 'inline-block';
                      btnAnnuler.style.display = 'none';
                      btnAnnuler.disabled = true;
                      depotDestinataires.disabled=false;
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
                                        '<td><button type="button" class="btn btn-danger delete-btn btn-sm">Supprimer</button></td>' +
                                        '</tr>';


                                    document.querySelector('#tableauVente tbody').insertAdjacentHTML('beforeend', newRow);
                                    // Calculer le total du montant
                                    
                                    var form = document.getElementById('formAjouterArticle');
                                    form.reset(); 
                                    quantiteInput.readOnly = true;
                                    btnAjouter.disabled = true;
                                                     

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
                alert("La ligne de Transfert est vide. Veuillez ajouter des éléments avant de valider.");
              } else {
              var confirmation = confirm("Êtes-vous sûr de vouloir valider ?");
                if (confirmation) {
                  var tableData = []; // Tableau pour stocker les données du tableau
                  // Capturer les données de la facture
                  var depotSource = document.getElementById('depotSource').value;
                  
                  var depotDestinataires = document.getElementById('depotDestinataire');
                  var commentaire = document.getElementById('commentaire').value;
                  var depotDestinataire = depotDestinataires.options[depotDestinataires.selectedIndex].text;
                  
                  var rows = tableauAchat.querySelectorAll('tbody tr');
                  
                  tableData.push({
                    depotSource : depotSource,
                    depotDestinataire: depotDestinataire,
                    commentaire: commentaire             
                  });
                  // Parcourir chaque ligne du tableau
                  rows.forEach(function(row) {
                    var rowData = {}; // Objet pour stocker les données de chaque ligne
                    var cells = row.querySelectorAll('td'); // Cellules de la ligne

                    // Capturer les données de chaque cellule
                    rowData.designation = cells[0].textContent;
                    rowData.quantite = cells[1].textContent;
                    rowData.quantiteAffichee = cells[2].textContent;
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
                    fetch('/transfertArticle/creer', {
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
                window.location = "{{ route('listeTransfert') }}";
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
    @endsection