<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleWidget;
use App\Models\User;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $setting;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // Ambil atau gunakan fallback pengaturan aplikasi
        $this->setting = AppSetting::getCached() ?? new AppSetting([
            'nama_aplikasi' => "MASJID JAMI' AL JIHAD",
            'footer' => 'Copyright &copy; 2026 Masjid Al-Jihad Dev. System',
        ]);

        // Share ke semua view secara otomatis
        view()->share('setting', $this->setting);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $widget = [
            'users' => \Illuminate\Support\Facades\Cache::remember('users_count_dashboard', 60, function () {
                return User::count();
            })
        ];

        return view('home', compact('widget'));
    }
}
