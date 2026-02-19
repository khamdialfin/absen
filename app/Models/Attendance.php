<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'letter_file',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────

    /**
     * Attendance dimiliki oleh satu User (siswa).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    /**
     * Scope: Filter berdasarkan tanggal hari ini.
     */
    public function scopeToday($query)
    {
        return $query->where('date', now()->toDateString());
    }

    /**
     * Scope: Filter berdasarkan bulan & tahun tertentu.
     */
    public function scopeMonth($query, int $month, int $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    // ─── Status Helpers ─────────────────────────────────────────

    /**
     * Tentukan status akhir hari berdasarkan time_in & time_out.
     */
    public function getFinalStatusAttribute(): string
    {
        // Jika sudah di-set manual (izin/sakit), langsung return
        if (in_array($this->status, ['izin', 'sakit', 'alfa'])) {
            return $this->status;
        }

        // Cek apakah terlambat (lewat jam 08:00)
        $isTerlambat = $this->time_in && $this->time_in > '08:00:00';

        // Cek apakah full (ada check-in DAN check-out)
        $isFull = $this->time_in && $this->time_out;

        if ($isTerlambat) {
            return 'terlambat';
        }

        if ($isFull) {
            return 'hadir';
        }

        if ($this->time_in && !$this->time_out) {
            return 'setengah_hari';
        }

        return 'alfa';
    }
}
