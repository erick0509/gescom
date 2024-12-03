@extends("layouts.header")
    @section("contenuPrincipale")
    <section  id="article-liste" class="article py-4 ">
      <div class="container">    
            <div class="row mt-5">
              <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h2>Facture d'achat</h2>
                <form class="mt-2 col-md-5 col-sm-6" method="post" action="{{ route('fournisseur.store') }}">
                    @csrf
                    <label class="form-label h6">Nouveau Fournisseur</label>
                    
                    <!-- Champ pour la désignation -->
                    <div class="input-group mb-3">
                        <input class="form-control" name="intitule" type="search" placeholder="Intitule" aria-label="Search">
                        <input class="form-control" name="contact" type="text" placeholder="Contact" aria-label="Unité">
                        <input class="form-control" name="adresse" type="text" placeholder="Adresse" aria-label="Quantité par Pack">
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
            <h1 class="h2">Nouvelle Facture #</h1>
            <div class="row d-flex text-center justify-content-center align-items-center">
                <form id="formAjouterFacture" class="row g-3">
                  
                  <!-- Sélection du client avec un dropdown (select) -->
                  <div class="mb-3 row">
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4" >Date d'achat:</label>
                            <div class="col-md-2 col-12">
                                <input type="date" class="form-control readonly-input" id="dateAchat" required>
                            </div>
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4">Numero Facture:</label>
                      
                      <div class="col-md-2 col-12">
                        <input type="text" class="form-control readonly-input" id="numeroFacture" required>
                      </div>
                      <label for="clientSelect" class="col-md-1 col-sm-2 col-form-label h4">Fournisseur:</label>
                      <div class="col-md-2 col-12">
                        <input type="text" class="form-control form-control-sm readonly-input" id="searchClient" placeholder="Saisir le nom du fournisseur" >
                      </div>
                      <div class="col-md-2 col-12">
                          <select class="form-select form-select-sm" id="clientSelect" required>
                              <option value="" disabled selected>Sélectionner un fournisseur</option>
                              @foreach($fournisseurs as $fournisseur)
                                  <option value="{{ $fournisseur->id }}" data-nom="{{ $fournisseur->intitule }}" 
                                  data-adresse="{{ $fournisseur->adresse }}" 
                                  data-contact="{{ $fournisseur->contact }}">{{ $fournisseur->intitule }}</option>
                              @endforeach
                          </select>
                      </div>
                  </div>

                  <!-- Affichage des informations du client (readonly) -->
                  <div class="mb-3 row">
                      <label for="nomClient" class="col-md-1 col-sm-2 col-form-label h4">Nom:</label>
                      <div class="col-md-2 col-12">
                          <input type="text" class="form-control form-control-sm" id="nom" readonly required>
                      </div>
                      <label for="contactClient" class="col-md-1 col-sm-2 col-form-label h4">Contact:</label>
                      <div class="col-md-2 col-12">
                          <input type="text" class="form-control form-control-sm" id="contact" readonly>
                      </div>
                      <label for="contactClient" class="col-md-1 col-sm-2 col-form-label h4">Adresse:</label>
                      <div class="col-md-2 col-12">
                          <input type="text" class="form-control form-control-sm" id="adresse" readonly>
                      </div>                      
                  </div>
                  <!-- Bouton pour ajouter des lignes de commande -->
                  <div class="d-flex justify-content-center">
                      <button type="button" id="btnAjouter" class="btn btn-primary mb-3 me-2">Ajouter des articles</button>
                  </div>
              </form>
            </div>
          <div id="ajouterDesArticles" style="display:none;" >
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            </div> 
            <h1 class="h2">les articles pour l'achat #</h1>
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
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4 mt-3">Quantite:</label>
                      <div class="col-md-2 col-12 mt-3">
                        <input type="number" step="0.01" class="form-control readonly-input-article" name="quantite" readonly required>
                      </div>
                      <label for="inputPassword" class="col-md-1 col-sm-2 col-form-label h4 mt-3" >Prix d'achat:</label>
                      <div class="col-md-2 col-12 mt-3">
                        <div class="input-group">
                          <span class="input-group-text">Ar</span>
                          <input type="number" class="form-control readonly-input-article" id="prixAchat" name="PrixAchat" step="0.01" readonly required>
                        </div>
                        
                      </div>
                      <div class="col-md-2 col-12 mt-3 ">
                          <button type="button" id="btnAjouterArticle"class="btn btn-primary mb-3 me-2 form-control" disabled>Ajouter</button> 
                      </div>
                    </div>
                </form>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-6 rounded-0">     
            </div>
            <div class="row mt-1" >
                <h1 class="h2">Achat #</h1>
                <div class="row d-flex text-center justify-content-center align-items-center ">
                  <div class="col-md-12">
                  <table class="table" id="tableauAchat">
                    <thead>
                      <tr>
                        <th scope="col">Designation</th>
                        <th scope="col">Qt</th>
                        <th scope="col">P.Achat</th>
                        <th scope="col">Montant</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                  </table>
                  </div>
                </div>
                <div class="d-flex justify-content-start mb-2">
                    <h3 id="totalMontant" class="me-3"></h3>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" checked>
                  <label class="form-check-label" for="flexRadioDefault1">
                    Au comptant
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" >
                  <label class="form-check-label" for="flexRadioDefault2">
                    A credit
                  </label>
                </div>
                <div class="d-flex justify-content-start mb-2">
                      <label for="inputPassword" class="col-md-2 col-2 col-form-label h4">Somme payee</label>
                      <div class="col-md-2 col-12">
                        <div class="input-group">
                          <span class="input-group-text">Ar</span>
                          <input type="number" class="form-control " name="sommePayee" id="sommePayee" step="0.01" readonly>
                        </div>
                      </div>
                </div>
                <div class="d-flex justify-content-start mb-2">
                      <label for="inputPassword" class="col-md-2 col-2 col-form-label h4">Mode de payement</label>
                      <div class="col-md-2 col-12">
                        <div class="input-group">
                          <input type="text" class="form-control "placeholder="en Espèce" name="mode" id="mode" required>
                        </div>
                      </div>
                </div>
                <div class="d-flex justify-content-start mb-2">
                      <label for="inputPassword" class="col-md-2 col-2 col-form-label h4">Référence : (optionnel)</label>
                      <div class="col-md-2 col-12">
                        <div class="input-group">
                          <input type="text" placeholder="CHEQUE N°" class="form-control " name="referencePayement" id="referencePayement" >
                        </div>
                      </div>
                </div>             
              </div> 
            <div class="d-flex justify-content-start mt-2">
              <button type="button" id="btnAnnuler"class="btn btn-warning mb-3 me-2" style="display:none"><i class="fas fa-times-circle"></i> Annuler</button>
              <button type="submit" id="btnValider" class="btn btn-success mb-3" ><i class="fas fa-check-circle"></i> Valider</button>
            </div>
          </div>           
        </div>       
    </section>
    <script>                  
                      document.addEventListener('DOMContentLoaded', function() {
                      var btnAjouter = document.getElementById('btnAjouter');
                      var btnAnnuler = document.getElementById('btnAnnuler');
                      var inputs = document.querySelectorAll('.readonly-input');
                      var ajouterDesArticles = document.getElementById('ajouterDesArticles');
                      var tableauDepots = document.getElementById('tableauAchat');

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
                        document.getElementById('clientSelect').disabled = true;
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
                      document.getElementById('clientSelect').disabled = false;
                      });
                      
                    });
                    ////Validation des champs
                    
    </script>
    <!--ajout des articles-->
    <script>
         function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }
          document.addEventListener('DOMContentLoaded', function() {
                      var btnAjouter = document.getElementById('btnAjouterArticle');
                      var inputs = document.querySelectorAll('.readonly-input-article');
                      var tableauDepots = document.getElementById('tableauAchat');
                      var totalElement = document.getElementById('totalMontant');
                      var quantiteInput = document.querySelector('input[name="quantite"]');
                      var prixAchatInput = document.querySelector('input[name="PrixAchat"]');
                      var sommePayee= document.querySelector('input[name="sommePayee"]');
                      //var ajouterDesArticles = document.getElementById('ajouterDesArticles');
                      var totalMontant = parseFloat(totalElement.textContent.trim()) || 0;
                  tableauDepots.addEventListener('click', function(event) {
                    if (event.target.classList.contains('delete-btn')) {
                      // Récupérer la ligne parente de l'élément de bouton supprimé
                      var row = event.target.closest('tr');
                      if (row) {
                        // Récupérer le montant de la ligne à supprimer
                        var montantRow = parseFloat(row.cells[3].textContent.trim());
                        // Soustraire le montant de la ligne supprimée du total
                        totalMontant -= montantRow;
                        totalElement.textContent = totalMontant.toFixed(2);
                        sommePayee.value = totalMontant.toFixed(2);

                        // Supprimer la ligne du tableau
                        row.remove();
                      }
                    }
                    });  
                      btnAjouter.addEventListener('click', function() {
                        var form = document.getElementById('formAjouterArticle');
                        if (form.checkValidity()) {
                          var selectElement = form.querySelector('select[name="articles"]');
                          var selectedOption = selectElement.options[selectElement.selectedIndex];
                          var article = selectedOption.text; 
                          var quantite = parseFloat(form.querySelector('input[name="quantite"]').value); // Convertir en nombre décimal
                          var prixAchat = parseFloat(form.querySelector('input[name="PrixAchat"]').value); // Convertir en nombre décimal
                          var total = prixAchat * quantite; // Calcul du total
                          if(((quantite!==null || quantite!==0) && quantite>0)&&((prixAchat!==null || prixAchat!==0) && prixAchat>0))
                          {
                              // Création d'une nouvelle ligne dans le tableau
                              var newRow = document.createElement('tr');
                              newRow.classList.add('row-to-delete');
                              newRow.innerHTML = `
                              <td>${article}</td>
                              <td>${quantite}</td>
                              <td>${prixAchat}</td>
                              <td>${total.toFixed(2)}</td>
                              <td><button class="btn btn-danger btn-sm delete-btn ">annuler</button>
                              `;
                              tableauDepots.querySelector('tbody').appendChild(newRow);

                              // Calcul et mise à jour du total global
                              totalMontant += total;
                              totalElement.textContent ="Total: "+formatNumber(totalMontant.toFixed(2))+" Ar"; // Affichage du total arrondi à 2 décimales
                              //totalElement.textContent=total;
                              sommePayee.value = totalMontant.toFixed(2);
                              // Effacer les champs du formulaire après l'ajout
                              form.reset(); 
                              quantiteInput.readOnly = true;
                              prixAchatInput.readOnly = true;
                              btnAjouter.disabled = true; 
                          }
                          else{
                            alert('Saisie incorrecte');
                          }         
                        } else {
                        // Si la validation échoue, affichez un message d'erreur ou effectuez une autre action
                          alert('Veuillez remplir tous les champs obligatoires.');
                        }
                      
                      });  
                      
                  });
                    
                    ////Validation des champs   
                            
    </script>
    <!-- Designation input-->
    <script>
            document.addEventListener('DOMContentLoaded', function() {
                var btnAjouter = document.getElementById('btnAjouterArticle');
                var designationInput= document.getElementById('designation');
                var selectDesignation = document.getElementById('selectDesignation');
                var quantiteInput = document.querySelector('input[name="quantite"]');
                var prixAchatInput = document.querySelector('input[name="PrixAchat"]');
                const inputDesi = document.getElementById('desi');
                function updateReadonlyState() {
                    if (selectDesignation.value !== selectDesignation.options[0].value) {
                        quantiteInput.readOnly = false;
                        prixAchatInput.readOnly = false;
                        btnAjouter.disabled = false;
                         var designation = selectDesignation.options[selectDesignation.selectedIndex].text;
                        var encodedDesignation = encodeURIComponent(designation);
                        // Vérifie si l'option sélectionnée est valide
                            fetch(`/get-article-price?designation=${encodedDesignation}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.prixMoyenAchat !== undefined) {
                                        document.getElementById('prixAchat').value = data.prixMoyenAchat; // Met à jour le champ "Prix d'achat"
                                    } else {
                                        console.error('Article not found');
                                    }
                                })
                                .catch(error => console.error('Error:', error));
                        
                    } else {
                        //
                        document.getElementById('prixAchat').value = ''; // Réinitialise le champ "Prix d'achat" si l'option sélectionnée est invalide
                        //
                        quantiteInput.readOnly = true;
                        prixAchatInput.readOnly = true;
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
    <!-- GESTION DE BOUTON -->
    
    <!-- JSON et bouton valider-->
    <script>
            document.addEventListener('DOMContentLoaded', function() {
            var btnValider = document.getElementById('btnValider'); // Bouton de validation du formulaire
            var tableauAchat = document.getElementById('tableauAchat'); // Tableau dynamique
            btnValider.addEventListener('click', function() {
              var rows = tableauAchat.querySelectorAll('tbody tr');
              var mode = document.getElementById('mode').value;
              var reference = document.getElementById('referencePayement').value;
              var inputSommePayee = document.getElementById('sommePayee').value;
              var montantTotal=0;
                  rows.forEach(function(row) {
                    var cells = row.querySelectorAll('td');
                      montantTotal+=parseFloat(cells[3].textContent);
                  });
              if (rows.length === 0 || mode.trim() === '' || montantTotal<parseFloat(inputSommePayee) || parseFloat(inputSommePayee)<0) {
                alert("La ligne d'achat est vide ou  Formulaires Vides ou Somme Payee invalide.");
              } else {
              var confirmation = confirm("Êtes-vous sûr de vouloir valider ?");
                if (confirmation) {
                  var tableData = []; // Tableau pour stocker les données du tableau
                  // Capturer les données de la facture
                  var radioComptant = document.getElementById('flexRadioDefault1');
                  var radioCredit = document.getElementById('flexRadioDefault2');
                  var numeroFacture = document.getElementById('numeroFacture').value;
                  idFournisseur=document.getElementById('clientSelect').value;
                  //var date = document.getElementById('date').value;
                  var statut="payee";
                  //ajouter d'abord la facture
                  if (montantTotal>parseFloat(inputSommePayee)) {
                     statut="non payee";
                  }
                  else{
                    statut="payee";
                  }
                  // Parcourir chaque ligne du tableau
                  var dateAchat = document.getElementById('dateAchat').value;
                  tableData.push({
                    dateAchat: dateAchat,
                    numeroFacture: numeroFacture,
                    idFournisseur: idFournisseur,
                    statut: statut,
                    sommePayee: inputSommePayee,
                    montantTotals:montantTotal.toFixed(2),
                    mode:mode,
                    reference:reference
                  });
                  
                  rows.forEach(function(row) {
                    var rowData = {}; // Objet pour stocker les données de chaque ligne
                    var cells = row.querySelectorAll('td'); // Cellules de la ligne

                    // Capturer les données de chaque cellule
                    rowData.designation = cells[0].textContent;
                    rowData.prix = cells[2].textContent;
                    rowData.quantite = cells[1].textContent;
                    rowData.montant = cells[3].textContent;

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
            fetch('/documentAchat/creerAchat', {
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
          window.location = "{{ route('documentachat') }}";
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
        document.addEventListener('DOMContentLoaded', function() {
          var radioComptant = document.getElementById('flexRadioDefault1');
          var radioCredit = document.getElementById('flexRadioDefault2');
          var inputSommePayee = document.getElementById('sommePayee');
          var tableauAchat = document.getElementById('tableauAchat');
          var montantTotal=0;
          
          // Écouter les changements d'état des boutons radio
          radioComptant.addEventListener('change', function() {
              if (radioComptant.checked) {
                  var rows = tableauAchat.querySelectorAll('tbody tr');
                  rows.forEach(function(row) {
                      var cells = row.querySelectorAll('td');
                      montantTotal+=parseFloat(cells[3].textContent);
                  });
                  inputSommePayee.readOnly = true; // Rendre le champ en lecture seule
                  inputSommePayee.value=montantTotal.toFixed(2);
                  montantTotal=0;
              }
          });

          radioCredit.addEventListener('change', function() {
              if (radioCredit.checked) {
                  inputSommePayee.readOnly = false; // Permettre la saisie dans le champ
              }
          });

          // Vérifier l'état initial des boutons radio au chargement de la page
          if (radioComptant.checked) {
              inputSommePayee.readOnly = true; // Champ en lecture seule par défaut
          } else {
              inputSommePayee.readOnly = false; // Champ non en lecture seule par défaut
          }
      });

    </script>
    <script>
      const selectDesignation = document.getElementById('selectDesignation');
      const inputDesi = document.getElementById('desi');
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
        if (option) {
          document.getElementById('nom').value = option.getAttribute('data-nom');
          document.getElementById('contact').value = option.getAttribute('data-contact');
          document.getElementById('adresse').value = option.getAttribute('data-adresse');
        } else {
          document.getElementById('nom').value = '';
          document.getElementById('contact').value = '';
          document.getElementById('adresse').value = '';
        }
    }
</script>
<script>
    document.getElementById('clientSelect').addEventListener('change', function() {
        const selectedClient = this.options[this.selectedIndex];
        document.getElementById('nom').value = selectedClient.getAttribute('data-nom');
        document.getElementById('contact').value = selectedClient.getAttribute('data-contact');
        document.getElementById('adresse').value = selectedClient.getAttribute('data-adresse');
    });
</script>
    @endsection