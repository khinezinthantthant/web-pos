<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_sale_overviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("total_vouchers");
            $table->double("total_cash");
            $table->double("total_tax");
            $table->double("total");
            // $table->integer("day");
            // $table->integer("month");
            // $table->integer("year");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sale_overviews');
    }
};
