<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// OTP: akses tanpa auth (karena user belum login). Kita pakai session 'otp_user_id'.
Route::get('/otp', [GoogleAuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/otp', [GoogleAuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/otp/resend', [GoogleAuthController::class, 'resendOtp'])->name('otp.resend');


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::post('/kategori', [KategoriController::class, 'store']);

    Route::get('/buku', [BukuController::class, 'index']);
    Route::post('/buku', [BukuController::class, 'store']);
});
