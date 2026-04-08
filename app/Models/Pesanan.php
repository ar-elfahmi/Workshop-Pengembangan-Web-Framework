<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'idpesanan';

    protected $fillable = [
        'idvendor',
        'user_id',
        'nama',
        'timestamp',
        'total',
        'metode_bayar',
        'status_bayar',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'snap_token',
        'snap_redirect_url',
        'payment_payload',
        'paid_at',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'paid_at' => 'datetime',
        'payment_payload' => 'array',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'idvendor', 'idvendor');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
    }
}
