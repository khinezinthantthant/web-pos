<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = ["voucher_number", "total", "tax", "net_total", "user_id"];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function voucher_records()
    {
        return $this->hasMany(VoucherRecord::class);
    }

    public function scopeThisWeek($builder)
    {
        $now = Carbon::now();
        $end_date = $now->format('Y-m-d');
        $start_date = $now->startOfWeek()->format('Y-m-d');
        return $builder->whereBetween('vouchers.created_at', [$start_date, $end_date]);
    }
}
