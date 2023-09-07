<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySaleOverview extends Model
{
    protected $fillable =["month","year","total_vouchers","total_cash","total_tax","total"];
    use HasFactory;
}
