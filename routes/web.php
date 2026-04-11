<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LabelPrintController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/canteen/checkout', function () {
    return view('pages.canteen.checkout', [
        'midtransClientKey' => (string) config('services.midtrans.client_key'),
        'midtransIsProduction' => (bool) config('services.midtrans.is_production'),
    ]);
})->name('canteen.checkout');

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

    Route::get('/ajax/wilayah', [WilayahController::class, 'ajaxPage'])->name('wilayah.ajax.page');
    Route::get('/axios/wilayah', [WilayahController::class, 'axiosPage'])->name('wilayah.axios.page');
    Route::get('/wilayah/provinsi', [WilayahController::class, 'getProvinsi'])->name('wilayah.provinsi');
    Route::post('/wilayah/kota', [WilayahController::class, 'getKota'])->name('wilayah.kota');
    Route::post('/wilayah/kecamatan', [WilayahController::class, 'getKecamatan'])->name('wilayah.kecamatan');
    Route::post('/wilayah/kelurahan', [WilayahController::class, 'getKelurahan'])->name('wilayah.kelurahan');

    Route::get('/ajax/pos', [PosController::class, 'ajaxPage'])->name('pos.ajax.page');
    Route::get('/axios/pos', [PosController::class, 'axiosPage'])->name('pos.axios.page');
    Route::post('/pos/find-item', [PosController::class, 'findItem'])->name('pos.find-item');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store']);

    Route::get('/buku', [BukuController::class, 'index'])->name('buku');
    Route::post('/buku', [BukuController::class, 'store']);
    Route::get('/buku/pdf', [BukuController::class, 'downloadPdf'])->name('buku.pdf');
    Route::get('/buku/labels', [LabelPrintController::class, 'index'])->name('buku.labels.index');
    Route::post('/buku/labels/pdf', [LabelPrintController::class, 'generatePdf'])->name('buku.labels.pdf');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportsController::class, 'downloadPdf'])->name('reports.pdf');

    Route::get('/vendor/menu', [VendorDashboardController::class, 'menuPage'])->name('vendor.menu');
    Route::get('/vendor/orders', [VendorDashboardController::class, 'ordersPage'])->name('vendor.orders');
    Route::post('/vendor/menu', [VendorDashboardController::class, 'storeMenu'])->name('vendor.menu.store');
    Route::get('/vendor/orders/lunas', [VendorDashboardController::class, 'paidOrders'])->name('vendor.orders.lunas');

    // Barang Pages
    Route::get('/barang', function () {
        return view('barang.index');
    })->name('barang.index');
    Route::get('/barang/datatables', function () {
        return view('barang.datatables');
    })->name('barang.datatables');
    Route::get('/barang/select', function () {
        return view('barang.select');
    })->name('barang.select');
});
