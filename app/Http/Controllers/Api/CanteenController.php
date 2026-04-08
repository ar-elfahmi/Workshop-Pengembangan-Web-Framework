<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Services\GuestUserService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CanteenController extends Controller
{
    public function __construct(
        private readonly GuestUserService $guestUserService,
        private readonly MidtransService $midtransService,
    ) {}

    public function vendors(): JsonResponse
    {
        $vendors = Vendor::query()
            ->select(['idvendor', 'nama_vendor'])
            ->orderBy('nama_vendor')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $vendors,
        ]);
    }

    public function menusByVendor(int $idvendor): JsonResponse
    {
        $vendor = Vendor::query()->findOrFail($idvendor);

        $menus = $vendor->menus()
            ->select(['idmenu', 'nama_menu', 'harga', 'path_gambar', 'idvendor'])
            ->orderBy('nama_menu')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'vendor' => $vendor,
                'menus' => $menus,
            ],
        ]);
    }

    public function createVendorMenu(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'idvendor' => ['required', 'integer', Rule::exists('vendor', 'idvendor')],
            'nama_menu' => ['required', 'string', 'max:150'],
            'harga' => ['required', 'integer', 'min:1'],
            'path_gambar' => ['nullable', 'string', 'max:255'],
        ]);

        $menu = Menu::query()->create($payload);

        return response()->json([
            'status' => 'success',
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu,
        ], 201);
    }

    public function createPesanan(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'idvendor' => ['required', 'integer', Rule::exists('vendor', 'idvendor')],
            'metode_bayar' => ['required', Rule::in(['va', 'qris'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.idmenu' => ['required', 'integer', Rule::exists('menu', 'idmenu')],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $result = DB::transaction(function () use ($payload) {
                $guestUser = $this->guestUserService->createGuestUser();

                $menuIds = collect($payload['items'])->pluck('idmenu')->unique()->values()->all();
                $menus = Menu::query()
                    ->where('idvendor', $payload['idvendor'])
                    ->whereIn('idmenu', $menuIds)
                    ->get()
                    ->keyBy('idmenu');

                if (count($menuIds) !== $menus->count()) {
                    abort(response()->json([
                        'status' => 'error',
                        'message' => 'Ada menu yang tidak valid untuk vendor terpilih.',
                    ], 422));
                }

                $detailsPayload = [];
                $itemDetails = [];
                $total = 0;

                foreach ($payload['items'] as $item) {
                    $menu = $menus->get((int) $item['idmenu']);
                    $jumlah = (int) $item['jumlah'];
                    $harga = (int) $menu->harga;
                    $subtotal = $harga * $jumlah;

                    $total += $subtotal;

                    $detailsPayload[] = [
                        'idmenu' => $menu->idmenu,
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                        'timestamp' => now(),
                        'catatan' => $item['catatan'] ?? null,
                    ];

                    $itemDetails[] = [
                        'id' => (string) $menu->idmenu,
                        'price' => $harga,
                        'quantity' => $jumlah,
                        'name' => $menu->nama_menu,
                    ];
                }

                $midtransOrderId = 'CANTEEN-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);

                $pesanan = Pesanan::query()->create([
                    'idvendor' => (int) $payload['idvendor'],
                    'user_id' => $guestUser->id,
                    'nama' => $guestUser->name,
                    'timestamp' => now(),
                    'total' => $total,
                    'metode_bayar' => $payload['metode_bayar'],
                    'status_bayar' => 'Pending',
                    'midtrans_order_id' => $midtransOrderId,
                ]);

                foreach ($detailsPayload as $detail) {
                    DetailPesanan::query()->create([
                        ...$detail,
                        'idpesanan' => $pesanan->idpesanan,
                    ]);
                }

                $snap = $this->midtransService->createSnapTransaction($pesanan, $itemDetails);

                $pesanan->update([
                    'snap_token' => $snap['token'] ?? null,
                    'snap_redirect_url' => $snap['redirect_url'] ?? null,
                    'payment_payload' => $snap,
                ]);

                return [
                    'pesanan' => $pesanan->fresh(['details', 'vendor']),
                    'payment' => [
                        'snap_token' => $snap['token'] ?? null,
                        'redirect_url' => $snap['redirect_url'] ?? null,
                    ],
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibuat',
                'data' => $result,
            ], 201);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $httpException) {
            return $httpException->getResponse();
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function paymentCallback(Request $request): JsonResponse
    {
        $notification = $request->all();

        if (! $this->midtransService->isValidSignature($notification)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature key',
            ], 403);
        }

        $pesanan = Pesanan::query()
            ->where('midtrans_order_id', $notification['order_id'] ?? '')
            ->first();

        if (! $pesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        $transactionStatus = (string) ($notification['transaction_status'] ?? 'pending');
        $fraudStatus = isset($notification['fraud_status']) ? (string) $notification['fraud_status'] : null;
        $paymentStatus = $this->midtransService->mapPaymentStatus($transactionStatus, $fraudStatus);

        $pesanan->update([
            'status_bayar' => $paymentStatus,
            'midtrans_transaction_id' => $notification['transaction_id'] ?? $pesanan->midtrans_transaction_id,
            'payment_payload' => $notification,
            'paid_at' => $paymentStatus === 'Lunas' ? now() : $pesanan->paid_at,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Callback processed',
            'data' => [
                'idpesanan' => $pesanan->idpesanan,
                'status_bayar' => $pesanan->status_bayar,
            ],
        ]);
    }

    public function updatePaymentStatus(Request $request, int $idpesanan): JsonResponse
    {
        $payload = $request->validate([
            'status_bayar' => ['required', Rule::in(['Pending', 'Lunas', 'Gagal'])],
        ]);

        $pesanan = Pesanan::query()->findOrFail($idpesanan);
        $pesanan->status_bayar = $payload['status_bayar'];

        if ($payload['status_bayar'] === 'Lunas' && ! $pesanan->paid_at) {
            $pesanan->paid_at = now();
        }

        $pesanan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status pembayaran diperbarui',
            'data' => $pesanan,
        ]);
    }

    public function vendorPaidOrders(int $idvendor): JsonResponse
    {
        Vendor::query()->findOrFail($idvendor);

        $orders = Pesanan::query()
            ->with(['details.menu'])
            ->where('idvendor', $idvendor)
            ->where('status_bayar', 'Lunas')
            ->latest('timestamp')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }
}
