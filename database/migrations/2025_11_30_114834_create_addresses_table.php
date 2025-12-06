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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('detail', 1000);
            $table->string('subdistrict_id')->nullable();  // ✅ Komerce Subdistrict ID
            $table->string('subdistrict_name')->nullable(); // ✅ For display
            $table->string('city_name')->nullable();        // ✅ City name
            $table->string('province')->nullable();         // ✅ Province name
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
