@extends('layouts.app')

@section('title','Verifikasi OTP')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">

                <div class="card-body">

                    <h4 class="card-title text-center mb-4">Verifikasi Login</h4>

                    <p class="text-center text-muted">
                        Masukkan 6 digit kode OTP yang telah dikirim ke email kamu
                    </p>

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
                    <form method="POST" action="{{ route('otp.verify') }}">
                        @csrf

                        <div class="form-group text-center">
                            <input
                                type="text"
                                name="otp"
                                class="form-control text-center"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                placeholder="000000"
                                required
                                autofocus
                                style="font-size: 24px; letter-spacing: 6px;"
                            >
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                Verifikasi
                            </button>
                        </div>
                    </form>

                    {{-- RESEND --}}
                    <div class="text-center mt-3">
                        <form method="POST" action="{{ route('otp.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link">
                                Kirim ulang kode OTP
                            </button>
                        </form>
                    </div>

                    <div class="text-center text-muted mt-2" style="font-size:13px;">
                        Kode berlaku selama 5 menit
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
