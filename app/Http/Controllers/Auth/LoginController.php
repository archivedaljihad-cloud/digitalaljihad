<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Tujuan redirect setelah login
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Menampilkan form login dengan setting aplikasi
    public function showLoginForm()
    {
        $settingRecord = AppSetting::getCached() ?? AppSetting::first();
        $setting = $settingRecord ? $settingRecord->toArray() : [];
        return view('auth.login', compact('setting'));
    }

    // Menampilkan pesan setelah login
    protected function redirectTo()
    {
        session()->flash('success', 'Anda berhasil login!');
        return $this->redirectTo;
    }

    // Override credentials to support username / role identifier (e.g. 'bendahara', 'admin', 'dkmsatu', 'dkm')
    protected function credentials(Request $request)
    {
        $input = trim((string) $request->input($this->username()));
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $inputLower = strtolower($input);
            if ($inputLower === 'bendahara' || str_contains($inputLower, 'bendahara') || str_contains($inputLower, 'utut')) {
                $input = 'bendahara@aljihad.com';
            } elseif ($inputLower === 'admin' || str_contains($inputLower, 'super') || str_contains($inputLower, 'suwardi')) {
                $input = 'archived.aljihad@gmail.com';
            } elseif ($inputLower === 'dkmsatu' || $inputLower === 'ketua' || str_contains($inputLower, 'dkmsatu') || str_contains($inputLower, 'hadi')) {
                $input = 'ketuadkm@aljihad.com';
            } elseif ($inputLower === 'dkm' || $inputLower === 'petugas' || $inputLower === 'operator') {
                $input = 'dkm@aljihad.com';
            } else {
                $found = \App\Models\User::where('name', 'ilike', "%{$input}%")->first();
                if ($found) {
                    $input = $found->email;
                }
            }
        }
        return [
            'email' => $input,
            'password' => (string) $request->input('password'),
        ];
    }

    // Override attemptLogin to authenticate strictly with current canonical default passwords
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $email = strtolower($credentials['email'] ?? '');
        $password = (string) ($credentials['password'] ?? '');

        // 1. Standard auth attempt against database hash
        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            return true;
        }

        // 2. Canonical default passwords fallback (Semua password lama dibersihkan secara permanen)
        $canonicalDefaults = [
            'bendahara@aljihad.com' => '#1.Bendahara',
            'archived.aljihad@gmail.com' => 'SuperUser1971',
            'ketuadkm@aljihad.com' => '*dkm1#aljihad',
            'dkm@aljihad.com' => '135dkmlJihad',
        ];

        if (isset($canonicalDefaults[$email]) && $password === $canonicalDefaults[$email]) {
            $user = \App\Models\User::where('email', $email)->first();
            if ($user) {
                if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
                    $user->password = \Illuminate\Support\Facades\Hash::make($password);
                    $user->save();
                }
                $this->guard()->login($user, $request->filled('remember'));
                return true;
            }
        }

        return false;
    }

    // Override logout untuk redirect ke halaman welcome
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.')
            ->header('Clear-Site-Data', '"cache", "storage"');
    }
}
