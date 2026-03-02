<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    /**
     * Menyiapkan data reports (ringkasan buku per kategori).
     */
    private function getReportsData(): array
    {
        $kategori = Kategori::with('buku')->get();

        $reports = $kategori->map(function ($k) {
            return [
                'kategori'         => $k->nama_kategori,
                'jumlah_buku'      => $k->buku->count(),
                'daftar_judul'     => $k->buku->pluck('judul')->implode(', '),
                'daftar_pengarang' => $k->buku->pluck('pengarang')->unique()->implode(', '),
            ];
        })->toArray();

        $totalKategori = $kategori->count();
        $totalBuku     = Buku::count();

        return compact('reports', 'totalKategori', 'totalBuku');
    }

    /**
     * Menampilkan halaman reports.
     */
    public function index()
    {
        $data = $this->getReportsData();
        return view('pages.reports.index', $data);
    }

    /**
     * Download PDF reports dalam format landscape.
     */
    public function downloadPdf()
    {
        $data = $this->getReportsData();

        $pdf = Pdf::loadView('pages.reports.pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-reports-' . date('Y-m-d') . '.pdf');
    }
}
