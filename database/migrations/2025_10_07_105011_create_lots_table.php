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
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number')->unique();
            $table->foreignId('tuman_id')->constrained('tumans')->onDelete('cascade');
            $table->foreignId('mahalla_id')->nullable()->constrained('mahallas')->onDelete('set null');
            $table->text('address')->nullable();
            $table->string('unique_number')->nullable();
            $table->string('zone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->text('location_url')->nullable();
            $table->string('master_plan_zone')->nullable();
            $table->boolean('yangi_uzbekiston')->default(false);
            $table->decimal('land_area', 12, 2)->nullable();
            $table->string('object_type')->nullable();
            $table->string('object_type_ru')->nullable();
            $table->decimal('construction_area', 12, 2)->nullable();
            $table->decimal('investment_amount', 20, 2)->nullable();
            $table->decimal('initial_price', 20, 2)->nullable();
            $table->date('auction_date')->nullable();
            $table->decimal('sold_price', 20, 2)->nullable();
            $table->string('winner_type')->nullable();
            $table->string('winner_name')->nullable();
            $table->string('winner_phone')->nullable();
            $table->enum('payment_type', ['muddatli', 'muddatli_emas'])->nullable();
            $table->string('basis')->nullable();
            $table->enum('auction_type', ['ochiq', 'yopiq'])->nullable();
            $table->string('lot_status')->default('active');
            $table->boolean('contract_signed')->default(false);
            $table->date('contract_date')->nullable();
            $table->string('contract_number')->nullable();
            $table->decimal('paid_amount', 20, 2)->default(0);
            $table->decimal('transferred_amount', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('auction_fee', 20, 2)->default(0);
            $table->decimal('incoming_amount', 20, 2)->default(0);
            $table->decimal('davaktiv_amount', 20, 2)->default(0);
            $table->decimal('auction_expenses', 20, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
