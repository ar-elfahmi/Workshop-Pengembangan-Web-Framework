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
                            <h4>Hello! Verifikasi Login</h4>
                            <h6 class="font-weight-light">Masukkan kode OTP untuk melanjutkan</h6>

                            {{-- ERROR --}}
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                            @endif

                            {{-- SUCCESS --}}
                            @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                            @endif

                            {{-- FORM OTP --}}
                            <form class="pt-3" method="POST" action="{{ route('otp.verify') }}">
                                @csrf

                                <div class="form-group">
                                    <label class="text-muted">Kode OTP</label>
                                    <input
                                        type="text"
                                        name="otp_code"
                                        class="form-control form-control-lg"
                                        maxlength="6"
                                        pattern="[0-9]{6}"
                                        placeholder="000000"
                                        required
                                        autofocus
                                        style="font-size: 24px; letter-spacing: 6px; text-align: center;">
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                        Verifikasi OTP
                                    </button>
                                </div>
                            </form>

                            {{-- SUCCESS --}}
                            @if (session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                            @endif

                            {{-- WARNING --}}
                            @if (session('warning'))
                            <div class="alert alert-warning mt-3">
                                {{ session('warning') }}
                            </div>
                            @endif

                            <div class="text-center text-muted mt-2" style="font-size:13px;">
                                Kode berlaku selama 5 menit
                            </div>

                            <div class="text-center mt-3">
                                <form method="POST" action="{{ route('otp.resend') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link">
                                        Kirim ulang kode OTP
                                    </button>
                                </form>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}" class="btn btn-link">
                                    Kembali ke Login
                                </a>
                            </div>
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