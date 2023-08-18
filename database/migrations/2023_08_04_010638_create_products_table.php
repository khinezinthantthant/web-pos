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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId("brand_id");
            $table->double("actual_price");
            $table->double("sale_price");
            $table->integer("total_stock")->default(0);
            $table->string("unit");
            $table->longText("more_information");
            $table->string("photo")->default(config('info.default_user_photo'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
