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
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('additional_agreement_id')->nullable()->constrained()->onDelete('cascade');

            $table->integer('payment_number');
            $table->date('planned_date');
            $table->date('deadline_date');
            $table->decimal('planned_amount', 15, 2);

            $table->date('actual_date')->nullable();
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);

            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
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
