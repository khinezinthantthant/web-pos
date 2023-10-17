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
        Schema::create('monthly_sale_overviews', function (Blueprint $table) {
            $table->id();
            $table->integer("month");
            $table->integer("year");
            $table->bigInteger("total_vouchers");
            $table->double("total_cash");
            $table->double("total_tax");
            $table->string('total_actual_price');
            $table->double("total");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_sale_overviews');
    }
};
