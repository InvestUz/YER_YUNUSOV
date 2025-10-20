<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            
            // Area measurements - need 4 decimals, moderate size
            $table->decimal('land_area', 12, 4)->nullable();              // Max: 99,999,999.9999 ha
            $table->decimal('construction_area', 15, 4)->nullable();       // Max: 99,999,999,999.9999 sq.m
            
            $table->string('object_type')->nullable();
            $table->string('object_type_ru')->nullable();
            
            // Financial values - need 2 decimals, VERY large numbers
            $table->decimal('investment_amount', 20, 2)->nullable();       // Max: 999,999,999,999,999,999.99
            $table->decimal('initial_price', 20, 2)->nullable();           // Max: 999,999,999,999,999,999.99
            $table->date('auction_date')->nullable();
            $table->decimal('sold_price', 20, 2)->nullable();              // Max: 999,999,999,999,999,999.99
            
            $table->string('winner_type')->nullable();
            $table->string('winner_name')->nullable();
            $table->string('winner_phone')->nullable();
            $table->enum('payment_type', ['muddatli', 'muddatsiz'])->nullable();
            $table->integer('wizard_step')->default(0);
            $table->string('basis')->nullable();
            $table->enum('auction_type', ['ochiq', 'yopiq'])->nullable();
            $table->string('lot_status')->default('active');
            $table->boolean('contract_signed')->default(false);
            $table->date('contract_date')->nullable();
            $table->string('contract_number')->nullable();
            
            // Payment amounts - large financial values
            $table->decimal('paid_amount', 20, 2)->default(0);
            $table->decimal('transferred_amount', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('auction_fee', 20, 2)->default(0);
            $table->decimal('incoming_amount', 20, 2)->default(0);
            $table->decimal('davaktiv_amount', 20, 2)->default(0);
            $table->decimal('auction_expenses', 20, 2)->default(0);
            
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};