<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    public function menuPage()
    {
        abort_unless(auth()->user()?->role === 'vendor', 403);

        return view('pages.vendor.menu');
    }

    public function ordersPage()
    {
        abort_unless(auth()->user()?->role === 'vendor', 403);

        return view('pages.vendor.orders');
    }

    public function storeMenu(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'vendor', 403);

        if (! $user->vendor_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akun vendor belum terhubung ke data vendor.',
            ], 422);
        }

        $payload = $request->validate([
            'nama_menu' => ['required', 'string', 'max:150'],
            'harga' => ['required', 'integer', 'min:1'],
            'path_gambar' => ['nullable', 'string', 'max:255'],
        ]);

        $menu = Menu::query()->create([
            ...$payload,
            'idvendor' => $user->vendor_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil ditambahkan.',
            'data' => $menu,
        ], 201);
    }

    public function paidOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user && $user->role === 'vendor', 403);

        if (! $user->vendor_id) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ]);
        }

        $orders = Pesanan::query()
            ->with(['details.menu'])
            ->where('idvendor', $user->vendor_id)
            ->where('status_bayar', 'Lunas')
            ->latest('timestamp')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }
}
