<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosTransaksiDetail extends Model
{
    protected $table = 'pos_transaksi_detail';
    protected $primaryKey = 'iddetail';
    public $timestamps = false;

    protected $fillable = [
        'idtransaksi',
        'kode_barang',
        'nama_barang',
        'harga',
        'jumlah',
        'subtotal',
    ];
}
