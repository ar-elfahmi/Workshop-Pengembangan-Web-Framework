<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Purple Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <!-- endinject -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/css/style.css') }}">
  <!-- End layout styles -->
  <link rel="shortcut icon" href="{{ asset('purple-free/dist/assets/images/favicon.png') }}" />
</head>

<body>
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5">
              <div class="brand-logo">
                <img src="{{asset('purple-free/dist/assets/images/logo.svg')}}">
              </div>
              <h4>Hello! let's get started</h4>
              <h6 class="font-weight-light">Sign in to continue.</h6>
              <form class="pt-3" method="post" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" name="email" placeholder="Username">
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password" placeholder="Password">
                </div>
                <div class="mt-3 d-grid gap-2">
                  <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input"> Keep me signed in </label>
                  </div>
                  <a href="#" class="auth-link text-primary">Forgot password?</a>
                </div>
                <div class="mb-2 d-grid gap-2">
                  <a href="{{ route('google.login') }}" class="btn btn-block btn-google auth-form-btn">
                    <i class="mdi mdi-google me-2"></i>Connect using Google
                  </a>
                </div>
                <div class="mb-2 d-grid gap-2">
                  <button type="button" class="btn btn-block btn-facebook auth-form-btn">
                    <i class="mdi mdi-facebook me-2"></i>Connect using facebook </button>
                </div>
                <div class="text-center mt-4 font-weight-light"> Don't have an account? <a href="register.html" class="text-primary">Create</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{asset('purple-free/dist/assets/vendors/js/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{asset('purple-free/dist/assets/js/off-canvas.js')}}"></script>
  <script src="{{asset('purple-free/dist/assets/js/misc.js')}}"></script>
  <script src="{{asset('purple-free/dist/assets/js/settings.js')}}"></script>
  <script src="{{asset('purple-free/dist/assets/js/todolist.js')}}"></script>
  <script src="{{asset('purple-free/dist/assets/js/jquery.cookie.js')}}"></script>
  <!-- endinject -->
</body>

</html>