<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Session\Session;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // 1. Dapatkan data user dari Google
            $googleUser = Socialite::driver('google')->user();

            // 2. Cari user di database berdasarkan email, atau buat baru jika belum ada
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'provider' => 'google',
                    'avatar' => $googleUser->getAvatar(),
                ]
            );

            // ==========================================
            // START: LOGIKA PENCEGATAN & PEMBUATAN OTP
            // ==========================================

            // 3. Buat 6 digit kode OTP acak
            $otpCode = rand(100000, 999999);

            // 4. Tentukan batas waktu kadaluarsa (misalnya 5 menit dari sekarang)
            $otpExpiresAt = Carbon::now()->addMinutes(5);

            // 5. Simpan kode dan batas waktu ke database user tersebut
            $user->otp_code = $otpCode;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();

            // 6. SIMPAN ID USER KE SESSION (Sangat Krusial!)
            // Karena kita BELUM menjalankan Auth::login(), kita harus menitipkan ID user 
            // ke Session agar halaman verifikasi OTP tahu siapa yang sedang mencoba login.
            Session::put('otp_user_id', $user->id);

            // 7. (Hanya untuk tahap Testing/Eksperimen) 
            // Titipkan juga OTP ke session agar bisa kita cetak di layar tanpa perlu kirim email dulu
            Session::flash('testing_otp', $otpCode);

            // 8. Redirect ke halaman form OTP (Route ini akan kita buat setelah ini)
            return redirect()->route('otp.verify');

        } catch (\Exception $e) {
            // Jika batal login atau ada error, kembalikan ke halaman login
            return redirect('/login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }
    }
}
