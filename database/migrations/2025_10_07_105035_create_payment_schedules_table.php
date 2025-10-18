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
            $table->foreignId('additional_agreement_id')->nullable()->constrained()->onDelete('set null');

            $table->integer('payment_number'); // Тўлов тартиб рақами

            // Planned (График)
            $table->date('planned_date'); // Режа бўйича тўлов санаси
            $table->date('deadline_date')->nullable(); // Охирги тўлов муддати
            $table->decimal('planned_amount', 15, 2); // Режа бўйича сумма

            // Actual (Амал)
            $table->date('actual_date')->nullable(); // Амалдаги тўлов санаси
            $table->decimal('actual_amount', 15, 2)->default(0); // Амалда тўланган сумма
            $table->decimal('difference', 15, 2)->default(0); // Фарқ (+/-)

            // Status
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');

            // Additional fields
            $table->string('payment_method')->nullable(); // Тўлов усули
            $table->string('reference_number')->nullable(); // Тўлов ҳужжат рақами
            $table->text('note')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('contract_id');
            $table->index('planned_date');
            $table->index('deadline_date');
            $table->index('status');
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
