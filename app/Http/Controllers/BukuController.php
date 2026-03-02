<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        $kategori = Kategori::all();
        $buku = Buku::with('kategori')->get();

        return view('pages.buku.index', compact('kategori', 'buku'));
    }

    public function store(Request $request)
    {
        Buku::create($request->all());
        return redirect()->back();
    }

    /**
     * Download PDF daftar buku dalam format portrait.
     */
    public function downloadPdf()
    {
        $buku = Buku::with('kategori')->get();

        $pdf = Pdf::loadView('pages.buku.pdf', compact('buku'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('daftar-buku-' . date('Y-m-d') . '.pdf');
    }
}

