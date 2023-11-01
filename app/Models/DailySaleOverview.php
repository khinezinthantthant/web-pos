<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySaleOverview extends Model
{
    use HasFactory;
    protected $fillable = ["total","total_cash","total_actual_price","total_tax","total_vouchers","voucher_number","user_id","day","month","year"];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function voucher_records()
    {
        return $this->hasMany(VoucherRecord::class);
    }
}
