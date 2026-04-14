<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSchedule extends Model
{
    protected $fillable = [
        'kelas',
        'morning_start',
        'morning_end',
        'afternoon_start',
        'afternoon_end',
        'late_threshold',
    ];

    /**
     * Cek apakah sesi pagi aktif berdasarkan waktu sekarang.
     */
    public function isMorningActive(): bool
    {
        $now = now()->format('H:i:s');
        return $now >= $this->morning_start && $now <= $this->morning_end;
    }

    /**
     * Cek apakah sesi sore aktif berdasarkan waktu sekarang.
     */
    public function isAfternoonActive(): bool
    {
        $now = now()->format('H:i:s');
        return $now >= $this->afternoon_start && $now <= $this->afternoon_end;
    }

    /**
     * Return sesi yang sedang aktif: 'morning', 'afternoon', atau null.
     */
    public function getActiveSession(): ?string
    {
        if ($this->isMorningActive())
            return 'morning';
        if ($this->isAfternoonActive())
            return 'afternoon';
        return null;
    }
}
