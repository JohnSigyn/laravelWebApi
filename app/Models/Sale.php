<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product(){
        return $this->hasMany(SaleProduct::class)->with('productName');
    }
    public function history(){
        return $this->hasMany(SaleHistory::class);
    }
}
