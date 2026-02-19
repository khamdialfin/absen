<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use Illuminate\Http\Request;

class BendaharaController extends Controller
{
    public function index()
    {
        // Dashboard Summary
        $totalMasuk = CashTransaction::where('type', 'pemasukan')->sum('amount');
        $totalKeluar = CashTransaction::where('type', 'pengeluaran')->sum('amount');
        $saldoAkhir = $totalMasuk - $totalKeluar;

        // Recent transactions for dashboard
        $recentTransactions = CashTransaction::with('user')->latest('date')->latest('id')->limit(5)->get();

        return view('bendahara.dashboard', compact('totalMasuk', 'totalKeluar', 'saldoAkhir', 'recentTransactions'));
    }

    // ── PEMASUKAN ─────────────────────────────────────────────────────
    public function pemasukan()
    {
        $transactions = CashTransaction::where('type', 'pemasukan')
            ->with('user')
            ->latest('date')
            ->latest('id')
            ->paginate(10);
            
        return view('bendahara.pemasukan', compact('transactions'));
    }

    public function storePemasukan(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        CashTransaction::create([
            'user_id' => auth()->id(),
            'type' => 'pemasukan',
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
        ]);

        return back()->with('success', 'Pemasukan berhasil dicatat.');
    }

    // ── PENGELUARAN ───────────────────────────────────────────────────
    public function pengeluaran()
    {
        $transactions = CashTransaction::where('type', 'pengeluaran')
            ->with('user')
            ->latest('date')
            ->latest('id')
            ->paginate(10);
            
        return view('bendahara.pengeluaran', compact('transactions'));
    }

    public function storePengeluaran(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        CashTransaction::create([
            'user_id' => auth()->id(),
            'type' => 'pengeluaran',
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'date' => $validated['date'],
        ]);

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    // ── LAPORAN ───────────────────────────────────────────────────────
    public function laporan(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $transactions = CashTransaction::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get(); // No pagination for report, normally

        $totalMasuk = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalKeluar = $transactions->where('type', 'pengeluaran')->sum('amount');
        $saldoPeriode = $totalMasuk - $totalKeluar;

        return view('bendahara.laporan', compact('transactions', 'startDate', 'endDate', 'totalMasuk', 'totalKeluar', 'saldoPeriode'));
    }

    public function destroy(CashTransaction $transaction)
    {
        $transaction->delete();
        return back()->with('success', 'Transaksi dihapus.');
    }
}
