<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ["name","company","agent","phone_no","description","user_id"];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stocks()
    {
        return $this->hasManyThrough(Stock::class,Product::class);
    }
    public function brands()
    {
        return $this->hasManyThrough(VoucherRecord::class, Product::class);
    }
    public function sales()
    {
        return $this->hasManyThrough(VoucherRecord::class, Product::class);
    }
    // public function voucherRecords()
    // {
    //     return $this->hasManyThrough(VoucherRecord::class, Product::class);
    // }

}
