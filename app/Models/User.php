<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $with = ['role'];
    protected $fillable = [
        'name', 'last_name', 'email', 'password', 'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        if (is_null($this->last_name)) {
            return "{$this->name}";
        }

        return "{$this->name} {$this->last_name}";
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        $roleName = strtolower(trim(optional($this->role)->name ?? ''));
        $checkRole = strtolower(trim($role));
        $email = strtolower($this->email ?? '');
        $name = strtolower($this->name ?? '');

        // 1. Akun Bendahara: jika nama/email mengandung bendahara atau role bendahara
        if ($roleName === 'bendahara' || str_contains($email, 'bendahara') || str_contains($name, 'bendahara')) {
            return $checkRole === 'bendahara';
        }

        // 2. Akun Admin:
        if (in_array($checkRole, ['admin', 'superadmin'])) {
            return in_array($roleName, ['admin', 'superadmin']) || str_contains($email, 'admin') || str_contains($name, 'admin');
        }

        // 3. Akun Petugas / Operator:
        if (in_array($checkRole, ['petugas', 'operator'])) {
            return in_array($roleName, ['petugas', 'operator']) || str_contains($email, 'petugas') || str_contains($name, 'petugas') || str_contains($email, 'operator') || str_contains($name, 'operator') || str_contains($email, 'dkm');
        }

        return $roleName === $checkRole;
    }
}
