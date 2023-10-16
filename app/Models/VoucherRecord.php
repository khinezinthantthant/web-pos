<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherRecord extends Model
{
    use HasFactory;
    protected $fillable = ["voucher_id", "product_id", "quantity", "cost"];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
