<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ["name","actual_price","sale_price","total_stock","unit","more_information","brand_id","user_id"];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function voucherRecord(){
        return $this->belongsTo(VoucherRecord::class);
    }
}
