<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ["name","actual_price","sale_price","total_stock","unit","more_information","brand_id","photo","user_id"];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function voucher_records(){
        return $this->hasMany(VoucherRecord::class);
    }
    public function vouchers(){
        return $this->belongsToMany(Voucher::class,VoucherRecord::class);
    }

    public function stocks(){
        return $this->hasMany(Stock::class);
    }
}
