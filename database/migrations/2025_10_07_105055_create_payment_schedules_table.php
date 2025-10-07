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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->integer('year');
            $table->string('month')->nullable();
            $table->date('payment_date');
            $table->decimal('planned_amount', 20, 2)->default(0);
            $table->decimal('actual_amount', 20, 2)->default(0);
            $table->decimal('difference', 20, 2)->default(0);
            $table->enum('payment_frequency', ['monthly', 'quarterly'])->default('monthly');
            $table->boolean('is_additional_agreement')->default(false);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};
