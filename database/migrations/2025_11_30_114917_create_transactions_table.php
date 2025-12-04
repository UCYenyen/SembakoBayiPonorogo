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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('no_resi')->nullable();
            $table->bigInteger('delivery_price')->default(0);
            $table->bigInteger('total_price')->default(0);
            
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('address_id')->constrained('addresses')->onDelete('cascade');
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->foreignId('delivery_id')->constrained('deliveries')->onDelete('cascade');
            $table->foreignId('shopping_cart_id')->constrained('shopping_carts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['delivery_id']);
            $table->dropForeign(['address_id']);
            $table->dropForeign(['shopping_cart_id']);
        });
        Schema::dropIfExists('transactions');
    }
};
