<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
    protected $primaryKey = 'idbuku';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'judul',
        'pengarang',
        'idkategori'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'idkategori');
    }
}
