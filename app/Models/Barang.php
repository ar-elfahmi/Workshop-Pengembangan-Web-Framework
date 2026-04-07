<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'idbarang';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama_barang',
        'harga',
    ];
}
