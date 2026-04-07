<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTransaksi extends Model
{
    protected $table = 'pos_transaksi';
    protected $primaryKey = 'idtransaksi';
    public $timestamps = false;

    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'total',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
    ];
}
