<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $token;
    protected string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = config('services.fonnte.token', '');
    }

    /**
     * Kirim pesan WhatsApp via Fonnte API.
     *
     * @param string $target  Nomor HP tujuan (format 62xxx)
     * @param string $message Isi pesan
     * @return bool
     */
    public function sendMessage(string $target, string $message): bool
    {
        if (empty($this->token)) {
            Log::warning('Fonnte: Token belum diatur di .env');
            return false;
        }

        if (empty($target)) {
            Log::warning('Fonnte: Nomor tujuan kosong');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                Log::info("Fonnte: Pesan terkirim ke {$target}");
                return true;
            }

            Log::warning("Fonnte: Gagal kirim ke {$target}", ['response' => $result]);
            return false;
        } catch (\Exception $e) {
            Log::error("Fonnte: Error kirim pesan - {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Kirim notifikasi presensi berangkat (check-in).
     */
    public function sendCheckInNotification(string $parentPhone, string $studentName, string $time, string $status): bool
    {
        $statusLabel = $status === 'terlambat' ? '⚠️ TERLAMBAT' : '✅ Tepat Waktu';

        $message = "📋 *NOTIFIKASI PRESENSI*\n\n"
            . "Assalamu'alaikum Bapak/Ibu,\n\n"
            . "Ananda *{$studentName}* telah melakukan presensi BERANGKAT.\n\n"
            . "🕐 Jam Masuk: *{$time}*\n"
            . "📌 Status: *{$statusLabel}*\n"
            . "📅 Tanggal: *" . now()->translatedFormat('l, d F Y') . "*\n\n"
            . "Terima kasih.\n"
            . "_Sistem Presensi Digital - HadirIN_";

        return $this->sendMessage($parentPhone, $message);
    }

    /**
     * Kirim notifikasi presensi pulang (check-out).
     */
    public function sendCheckOutNotification(string $parentPhone, string $studentName, string $timeIn, string $timeOut): bool
    {
        $message = "📋 *NOTIFIKASI PRESENSI*\n\n"
            . "Assalamu'alaikum Bapak/Ibu,\n\n"
            . "Ananda *{$studentName}* telah melakukan presensi PULANG.\n\n"
            . "🕐 Jam Masuk: *{$timeIn}*\n"
            . "🕐 Jam Pulang: *{$timeOut}*\n"
            . "📅 Tanggal: *" . now()->translatedFormat('l, d F Y') . "*\n\n"
            . "Terima kasih.\n"
            . "_Sistem Presensi Digital - HadirIN_";

        return $this->sendMessage($parentPhone, $message);
    }
}
