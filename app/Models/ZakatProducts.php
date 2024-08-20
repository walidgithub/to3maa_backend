<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'productName',
        'productPrice',
        'productDesc',
        'productImage',
        'sa3Weight',
        'productQuantity'
    ];

    public function zakat() {
        return $this->belongsTo(Zakat::class);
    }
}
