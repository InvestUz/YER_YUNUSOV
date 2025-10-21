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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('lots')->onDelete('cascade');
            $table->string('contract_number');
            $table->date('contract_date');
            $table->decimal('contract_amount', 20, 2);
            $table->decimal('initial_paid_amount', 20, 2)->default(0);
            $table->date('initial_payment_date')->nullable();
            $table->enum('payment_type', ['muddatli', 'muddatsiz'])->default('muddatli');
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->string('buyer_inn')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->text('note')->nullable();
            $table->decimal('paid_amount', 20, 2)->default(0);
            $table->decimal('remaining_amount', 20, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
