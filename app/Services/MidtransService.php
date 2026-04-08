<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    public function createSnapTransaction(Pesanan $pesanan, array $itemDetails): array
    {
        $serverKey = $this->getServerKey();
        $snapUrl = $this->getSnapUrl();

        $enabledPayments = $pesanan->metode_bayar === 'qris'
            ? ['gopay', 'qris']
            : ['bank_transfer'];

        $payload = [
            'transaction_details' => [
                'order_id' => $pesanan->midtrans_order_id,
                'gross_amount' => (int) $pesanan->total,
            ],
            'customer_details' => [
                'first_name' => $pesanan->nama,
            ],
            'item_details' => $itemDetails,
            'enabled_payments' => $enabledPayments,
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hour',
                'duration' => 24,
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($snapUrl, $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Gagal membuat transaksi Midtrans: ' . $response->body());
        }

        return $response->json();
    }

    public function isValidSignature(array $notification): bool
    {
        $serverKey = $this->getServerKey();

        $expected = hash(
            'sha512',
            ($notification['order_id'] ?? '')
                . ($notification['status_code'] ?? '')
                . ($notification['gross_amount'] ?? '')
                . $serverKey
        );

        return hash_equals($expected, (string) ($notification['signature_key'] ?? ''));
    }

    public function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus = null): string
    {
        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'challenge' ? 'Pending' : 'Lunas';
        }

        if (in_array($transactionStatus, ['settlement'], true)) {
            return 'Lunas';
        }

        if (in_array($transactionStatus, ['pending'], true)) {
            return 'Pending';
        }

        if (in_array($transactionStatus, ['cancel', 'deny', 'expire', 'failure'], true)) {
            return 'Gagal';
        }

        return 'Pending';
    }

    private function getServerKey(): string
    {
        $serverKey = trim((string) config('services.midtrans.server_key'));

        if ($serverKey === '') {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum di-set di file .env');
        }

        if (str_contains($serverKey, 'Mid-client')) {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY terisi Client Key. Gunakan Server Key Midtrans.');
        }

        return $serverKey;
    }

    private function getSnapUrl(): string
    {
        $configured = trim((string) config('services.midtrans.snap_url'));
        if ($configured !== '') {
            return $configured;
        }

        $isProduction = (bool) config('services.midtrans.is_production');

        return $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }
}
