<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin - Register</title>
    <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('purple-free/dist/assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('purple-free/dist/assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-5 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <img src="{{ asset('purple-free/dist/assets/images/logo.svg') }}">
                            </div>
                            <h4>Buat akun baru</h4>
                            <h6 class="font-weight-light">Pilih role Admin atau Vendor.</h6>

                            @if ($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-lg" name="name" value="{{ old('name') }}" placeholder="Name" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" name="email" value="{{ old('email') }}" placeholder="Email" required>
                                </div>
                                <div class="form-group">
                                    <select class="form-control form-control-lg" name="role" required>
                                        <option value="">Pilih Role</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="vendor" {{ old('role') === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" name="password_confirmation" placeholder="Confirm Password" required>
                                </div>
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">REGISTER</button>
                                </div>
                                <div class="text-center mt-4 font-weight-light"> Sudah punya akun? <a href="{{ route('login') }}" class="text-primary">Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('purple-free/dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('purple-free/dist/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('purple-free/dist/assets/js/misc.js') }}"></script>
    <script src="{{ asset('purple-free/dist/assets/js/settings.js') }}"></script>
    <script src="{{ asset('purple-free/dist/assets/js/todolist.js') }}"></script>
    <script src="{{ asset('purple-free/dist/assets/js/jquery.cookie.js') }}"></script>
</body>

</html>