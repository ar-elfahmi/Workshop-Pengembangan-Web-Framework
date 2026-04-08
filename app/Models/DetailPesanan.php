<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'iddetail_pesanan';

    protected $fillable = [
        'idmenu',
        'idpesanan',
        'jumlah',
        'harga',
        'subtotal',
        'timestamp',
        'catatan',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'idpesanan', 'idpesanan');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'idmenu', 'idmenu');
    }
}
