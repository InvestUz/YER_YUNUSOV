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
        Schema::create('mahallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tuman_id')->constrained('tumans')->onDelete('cascade');
            $table->string('name');
            $table->string('name_ru')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahallas');
    }
};
