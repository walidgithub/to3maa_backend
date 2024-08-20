<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zakat extends Model
{
    use HasFactory;

    protected $fillable = [
        'membersCount',
        'zakatValue',
        'remainValue'
    ];

    public function zakatProducts() {
        return $this->hasMany(ZakatProducts::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
