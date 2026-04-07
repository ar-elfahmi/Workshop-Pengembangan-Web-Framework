<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PosTransaksi;
use App\Models\PosTransaksiDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function ajaxPage()
    {
        return view('pages.pos.ajax');
    }

    public function axiosPage()
    {
        return view('pages.pos.axios');
    }

    public function findItem(Request $request): JsonResponse
    {
        $request->validate([
            'kode' => 'required|string|max:20',
        ]);

        $barang = Barang::where('kode', $request->kode)->first();

        if (! $barang) {
            return response()->json([
                'status' => 'error',
                'code' => 404,
                'message' => 'Data barang tidak ditemukan',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Data received successfully',
            'data' => $barang,
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.kode' => 'required|string|max:20',
            'items.*.nama' => 'required|string|max:255',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        $items = $request->items;
        $calculatedTotal = collect($items)->sum(function (array $item) {
            return ((int) $item['harga']) * ((int) $item['jumlah']);
        });

        try {
            DB::beginTransaction();

            $kodeTransaksi = 'TRX' . now()->format('YmdHis') . random_int(100, 999);

            $transaksi = PosTransaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal_transaksi' => now(),
                'total' => $calculatedTotal,
            ]);

            foreach ($items as $item) {
                PosTransaksiDetail::create([
                    'idtransaksi' => $transaksi->idtransaksi,
                    'kode_barang' => $item['kode'],
                    'nama_barang' => $item['nama'],
                    'harga' => (int) $item['harga'],
                    'jumlah' => (int) $item['jumlah'],
                    'subtotal' => ((int) $item['harga']) * ((int) $item['jumlah']),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Data received successfully',
                'data' => [
                    'idtransaksi' => $transaksi->idtransaksi,
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'total' => $transaksi->total,
                ],
            ]);
        } catch (\Throwable $exception) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Gagal menyimpan transaksi',
                'data' => null,
            ], 500);
        }
    }
}
