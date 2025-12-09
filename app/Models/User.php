<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'is_blocked' => 'boolean',
        ];
    }

    // ===================================
    // RELATIONS (SUMBER ERROR LINTER)
    // ===================================

    // Relasi ke Loans (Peminjaman)
    public function loans(): HasMany // <-- Digunakan di CatalogController
    {
        return $this->hasMany(Loan::class);
    }

    public function fines(): HasManyThrough
    {
        return $this->hasManyThrough(Fine::class, Loan::class);
    }

    // Relasi ke Reviews (Ulasan)
    public function reviews(): HasMany // <-- Digunakan di CatalogController
    {
        return $this->hasMany(Review::class);
    }

    // Relasi ke Notifikasi
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // ===================================
    // ROLE HELPERS (SUMBER ERROR isMahasiswa)
    // ===================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPegawai(): bool
    {
        return $this->role === 'pegawai';
    }

    public function isMahasiswa(): bool // <-- Digunakan di CatalogController
    {
        return $this->role === 'mahasiswa';
    }
}
