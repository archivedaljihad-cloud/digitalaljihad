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

        // Fallback cerdas jika user bendahara belum termigrasi role_id nya
        if ($checkRole === 'bendahara' && $roleName !== 'bendahara') {
            $email = strtolower($this->email ?? '');
            $name = strtolower($this->name ?? '');
            if (str_contains($email, 'bendahara') || str_contains($name, 'bendahara')) {
                return true;
            }
        }

        // Fallback cerdas jika user petugas/operator belum termigrasi role_id nya
        if (in_array($checkRole, ['petugas', 'operator']) && !in_array($roleName, ['petugas', 'operator'])) {
            $email = strtolower($this->email ?? '');
            $name = strtolower($this->name ?? '');
            if (str_contains($email, 'petugas') || str_contains($name, 'petugas') || str_contains($email, 'operator') || str_contains($name, 'operator')) {
                return true;
            }
        }

        if (in_array($checkRole, ['admin', 'superadmin'])) {
            return in_array($roleName, ['admin', 'superadmin']);
        }

        if (in_array($checkRole, ['petugas', 'operator'])) {
            return in_array($roleName, ['petugas', 'operator']);
        }

        return $roleName === $checkRole;
    }
}
