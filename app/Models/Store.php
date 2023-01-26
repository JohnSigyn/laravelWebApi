<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
   protected $fillable = [
        'name',
        'address',
        'phone',
        'proprietor',
        'gst',
        'pan',
        'gst_applicable',
        'low_stock',
        'loyalty_given',
        'loyalty_value',
        'expire_date',
        'plan_type',
        "invoice",
        'paper_size',
   ];

//    public function image()
//     {
//         return $this->morphOne(Image::class, 'imageable');
//     }

//     public function licenses()
//     {
//         return $this->hasMany(License::class);
//     }

//     public function activeLicense()
//     {
//         return $this->hasOne(License::class)->where('active', 1);
//     }

//     public function inactiveLicenses()
//     {
//         return $this->hasMany(License::class)->with('plan')->where('active', 0);
//     }

//     public function purchases() {
//         return $this->hasMany(Purchase::class)->with('supplier');
//     }

//     public function outstandingPurchases()
//     {
//         return $this->hasMany(Purchase::class)->whereRaw('payable_amount != paid_amount')->with('supplier');
//     }

//     public function sales()
//     {
//         return $this->hasMany(Sales::class)->with('customer');
//     }

//     public function salesHistory()
//     {
//         return $this->hasMany(SaleHistory::class);
//     }

//     public function expenditures()
//     {
//         return $this->hasMany(Expenditure::class);
//     }
}
