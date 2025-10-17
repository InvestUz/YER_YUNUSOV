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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_schedule_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('additional_agreement_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('category', [
                'city_budget',
                'development_fund',
                'shaykhontohur_budget',
                'new_uzbekistan',
                'yangikhayot_technopark',
                'ksz_directorates',
                'tashkent_city_directorate',
                'district_budgets'
            ]);
            $table->decimal('allocated_amount', 20, 2);
            $table->date('distribution_date');
            $table->enum('status', ['pending', 'distributed', 'cancelled'])->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('contract_id');
            $table->index('payment_schedule_id');
            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
