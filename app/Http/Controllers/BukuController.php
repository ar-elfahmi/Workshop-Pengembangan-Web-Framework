<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
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
}

