@extends("layouts.master")
@section("contenu")
<section class="login d-flex align-items-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-8 align-items-center">
        <h3 class="bg-gray p-4">Configuration du mots de passe de l'utilisateur</h3>
        @if($errors->any())
      @foreach($errors->all() as $error)
      <div class="alert alert-danger">{{ $error }}</div>
    @endforeach
    @endif
        <form method="post" action="{{route('auth.update')}}" class="needs-validation" novalidate>
          @csrf
          <fieldset class="p-4">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="floatingInput" placeholder="User" name="name"
                value="{{ session('name') }}" disabled>
              <label for="floatingInput">Utilisateur</label>
              <div class="invalid-feedback">
                Veuiller inserer votre nom d'utilisateur!
              </div>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="floatingPassword" placeholder="mots de passe"
                name="oldPassword" required>
              <label for="floatingPassword">Ancien Mots de passe</label>
              <div class="invalid-feedback">
                Veuiller saisir votre ancien mots de passe
              </div>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="floatingPassword" placeholder="mots de passe"
                name="newPassword" required>
              <label for="floatingPassword">Nouveau Mots de passe</label>
              <div class="invalid-feedback">
                Veuiller saisir votre nouveau mots de passe
              </div>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="floatingPassword" placeholder="mots de passe"
                name="renewPassword" required>
              <label for="floatingPassword">Confirmer le Nouveau Mots de passe</label>
              <div class="invalid-feedback">
                Veuiller resaisir votre nouveau mots de passe
              </div>
            </div>
            <div class="row">
              <a href="{{route('accueil')}}" type="submit" class="btn btn-secondary font-weight-bold mt-2 col-4 me-3"><i
                  class="fas fa-times-circle me-1"></i>Annuler</a>
              <button type="submit" class="btn btn-primary font-weight-bold mt-2 col-4"><i
                  class="fas fa-check-circle me-1"></i>Modifier</button>
            </div>
          </fieldset>
        </form>
      </div>
    </div>
  </div>
  </div>
</section>

<script src="assets/dist/js/bootstrap.bundle.min.js"></script>
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
</script>
@endsection