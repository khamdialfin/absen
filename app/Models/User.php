<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'role',
        'kelas',
        'no_absen',
        'gender',
        'nis',
        'profile_completed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'profile_completed' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────

    /**
     * User memiliki banyak data attendance.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // ─── Role Helpers ───────────────────────────────────────────

    public function isWalikelas(): bool
    {
        return $this->role === 'walikelas';
    }

    public function isSekertaris(): bool
    {
        return $this->role === 'sekertaris';
    }

    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Cek apakah user sudah menetapkan password.
     */
    public function hasPassword(): bool
    {
        return !is_null($this->password);
    }

    /**
     * Cek apakah profil sudah lengkap.
     */
    public function hasProfileCompleted(): bool
    {
        return $this->profile_completed;
    }
}
