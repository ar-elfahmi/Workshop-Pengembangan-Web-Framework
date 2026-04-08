<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'idvendor';

    protected $fillable = [
        'nama_vendor',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'idvendor', 'idvendor');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'idvendor', 'idvendor');
    }
}
