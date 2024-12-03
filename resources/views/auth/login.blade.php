@extends("layouts.master")
@section("contenu")
<section class="login d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-8 align-items-center">
          <h3 class="bg-gray p-4">Bienvenue dans votre gestion commerciale, Identifiez-vous!</h3>         
              @error('user')
                <div class="alert alert-danger">
                  {{$message}}
                </div>
              @enderror
              @if(session()->has('inactive'))
                  <div class="alert alert-warning">{{ session('inactive') }}</div>
              @endif
              @if(session()->has('success'))
                  <div class="alert alert-success">{{ session('success') }}</div>
              @endif
          <div class="text-center mb-1">
            <i class="fas fa-user-circle fa-5x text-primary"></i>
          </div>
          <form method="post" action="{{route('auth.login')}}" class="needs-validation" novalidate>
            @csrf
            <fieldset class="p-4">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder="User" name="name" required value="{{old('name')}}">
                    <label for="floatingInput">Utilisateur</label>
                    <div class="invalid-feedback">
                        Veuiller inserer votre nom d'utilisateur!
                    </div>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="mots de passe" name="password" required>
                    <label for="floatingPassword">Mots de passe</label>
                    <div class="invalid-feedback">
                        Veuiller saisir votre mots de passe
                    </div>
                  </div>
              <!--
              <div class="loggedin-forgot">
                <input type="checkbox" id="keep-me-logged-in" name="keep-me-logged-in">
                <label for="keep-me-logged-in" class="pt-2 pb-2">se souvenir de moi</label>
              </div>-->
              <div class="row justify-content-center">
                <button type="submit" class="btn btn-primary font-weight-bold mt-2 col-6"><i class="fas fa-sign-in-alt me-1"></i>Se connecter</button>
              </div>
            </fieldset>
          </form>
          <form id="formReinitialiser" action="{{route('auth.reinitialiser')}}" method="post">
              @csrf
              <a type="submit" class="mt-2 d-block text-secondary" onclick="reinitialiser()">mots de passe oublie?</a>
          </form>
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
      </div>
    </div>
  </div>
    </section>
    
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
    <script>
        (function () {
          'use strict'
          var forms = document.querySelectorAll('.needs-validation')
          Array.prototype.slice.call(forms)
            .forEach(function (form) {
              form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                  event.preventDefault()
                  event.stopPropagation()
                }
                form.classList.add('was-validated')
              }, false)
            })
        })()

        function reinitialiser() {
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
                        if (confirm('Voulez-vous vraiment réinitialiser le mot de passe?')) {
                            document.getElementById('formReinitialiser').submit();
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