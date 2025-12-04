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
        Schema::create('shopping_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_cart_id')->constrained('shopping_carts')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_cart_items', function (Blueprint $table) {
            $table->dropForeign(['shopping_cart_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('shopping_cart_items');
    }
};
