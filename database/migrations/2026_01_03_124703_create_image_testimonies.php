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
        Schema::create('image_testimonies', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->foreignId('testimony_id')->constrained('testimonies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('image_testimonies', function (Blueprint $table) {
            $table->dropForeign(['testimony_id']);
        });
        Schema::dropIfExists('image_testimonies');
    }
};
