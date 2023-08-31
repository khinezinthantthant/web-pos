<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySaleOverview extends Model
{
    use HasFactory;
    protected $fillable = ["total","total_cash","total_tax","total_vouchers","day","month","year"];
}
