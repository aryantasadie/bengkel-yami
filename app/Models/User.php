<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'karyawan_id',
        'username',
        'password',
        'nama',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ==================== Relationships ====================

    /**
     * User belongs to a Karyawan (optional).
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // ==================== Role Helpers ====================

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::OWNER;
    }

    public function isKaryawan(): bool
    {
        return $this->role === UserRole::KARYAWAN;
    }
}
