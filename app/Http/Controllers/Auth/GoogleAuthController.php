<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session; // Wajib
use Illuminate\Support\Facades\Mail; // Wajib untuk mengirim email
use Carbon\Carbon; // Wajib untuk waktu kadaluarsa
use Illuminate\Http\Request;
use App\Mail\OtpMail; // Import OtpMail

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            // dd($googleUser);

            if (empty($googleUser->email)) {
                return redirect()->route('login')->withErrors('No email returned from Google.');
            }

            // 1. Sinkronisasi Data User
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$user) {
                $user = User::create([
                    'name'      => $googleUser->name,
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'provider'  => 'google',
                    'avatar'    => $googleUser->avatar,
                    'password'  => bcrypt(Str::random(24)),
                ]);
            } else if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->id,
                    'provider'  => 'google',
                ]);
            }

            // 2. LOGIKA OTP LINIER (Simpan di Session)
            $otpCode = rand(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(5); // OTP berlaku 5 menit

            // Simpan data ke session
            Session::put('temp_user_id', $user->id);
            Session::put('otp_code', $otpCode);
            Session::put('otp_expires_at', $expiresAt);

            // Kirim email OTP
            try {
                Mail::to($user->email)->send(new OtpMail($user, $otpCode));
                Log::info('OTP email sent to: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email: ' . $e->getMessage());
                // Tetap lanjutkan meskipun email gagal, tapi tampilkan pesan peringatan
                Session::flash('warning', 'Gagal mengirim email OTP. Silakan coba kembali atau gunakan resend OTP.');
            }

            // [OPSIONAL] Munculkan di layar untuk testing
            Session::flash('testing_otp', $otpCode);

            // 3. REDIRECT KE HALAMAN OTP (Bukan Login)
            return redirect()->route('otp.form');
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors('Authentication failed.');
        }
    }

    // Menampilkan form verifikasi
    public function showOtpForm()
    {
        if (!Session::has('temp_user_id')) {
            return redirect()->route('login')->withErrors('Silakan login via Google terlebih dahulu.');
        }
        return view('auth.otp');
    }

    // Memproses angka OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp_code' => 'required|numeric']);

        $sessionOtp = Session::get('otp_code');
        $expiresAt = Session::get('otp_expires_at');
        $tempUserId = Session::get('temp_user_id');

        // Cek apakah session data ada
        if (!$sessionOtp || !$expiresAt || !$tempUserId) {
            return back()->withErrors(['otp_code' => 'Sesi login kadaluarsa. Silakan login kembali.']);
        }

        // Cek apakah kode cocok dan belum kadaluarsa
        if ($request->otp_code == $sessionOtp && Carbon::now()->isBefore($expiresAt)) {
            $user = User::find($tempUserId);

            if ($user) {
                // Eksekusi Login Resmi
                Auth::login($user);

                // Bersihkan Session
                Session::forget(['temp_user_id', 'otp_code', 'otp_expires_at']);

                return redirect()->route('dashboard');
            }
        }

        return back()->withErrors(['otp_code' => 'Kode OTP salah atau sudah kadaluarsa.']);
    }

    // Fungsi untuk resend OTP
    public function resendOtp(Request $request)
    {
        if (!Session::has('temp_user_id')) {
            return redirect()->route('login')->withErrors('Silakan login via Google terlebih dahulu.');
        }

        $user = User::find(Session::get('temp_user_id'));
        if (!$user) {
            return redirect()->route('login')->withErrors('User tidak ditemukan.');
        }

        // Generate OTP baru
        $otpCode = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(5);

        // Update session
        Session::put('otp_code', $otpCode);
        Session::put('otp_expires_at', $expiresAt);

        // Kirim email OTP baru
        try {
            Mail::to($user->email)->send(new OtpMail($user, $otpCode));
            Log::info('New OTP email sent to: ' . $user->email);

            return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP email: ' . $e->getMessage());
            return back()->withErrors('Gagal mengirim email OTP. Silakan coba lagi.');
        }
    }
}
