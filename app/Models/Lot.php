<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lot_number', 'tuman_id', 'mahalla_id', 'address', 'unique_number',
        'zone', 'latitude', 'longitude', 'location_url', 'master_plan_zone',
        'yangi_uzbekiston', 'land_area', 'object_type', 'object_type_ru',
        'construction_area', 'investment_amount', 'initial_price', 'auction_date',
        'sold_price', 'winner_type', 'winner_name', 'winner_phone', 'payment_type',
        'basis', 'auction_type', 'lot_status', 'contract_signed', 'contract_date',
        'contract_number', 'paid_amount', 'transferred_amount', 'discount',
        'auction_fee', 'incoming_amount', 'davaktiv_amount', 'auction_expenses'
    ];

    protected $casts = [
        'yangi_uzbekiston' => 'boolean',
        'contract_signed' => 'boolean',
        'auction_date' => 'date',
        'contract_date' => 'date',
        'land_area' => 'decimal:2',
        'construction_area' => 'decimal:2',
        'investment_amount' => 'decimal:2',
        'initial_price' => 'decimal:2',
        'sold_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'transferred_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'auction_fee' => 'decimal:2',
        'incoming_amount' => 'decimal:2',
        'davaktiv_amount' => 'decimal:2',
        'auction_expenses' => 'decimal:2',
    ];

    public function tuman()
    {
        return $this->belongsTo(Tuman::class);
    }

    public function mahalla()
    {
        return $this->belongsTo(Mahalla::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    // Automatic calculations
    public function calculateAuctionFee()
    {
        if ($this->sold_price) {
            $this->auction_fee = $this->sold_price * 0.01;
        }
        return $this->auction_fee;
    }

    public function calculateIncomingAmount()
    {
        $this->incoming_amount = $this->transferred_amount - $this->discount - $this->auction_fee;
        return $this->incoming_amount;
    }

    public function calculateDavaktivAmount()
    {
        $this->davaktiv_amount = $this->incoming_amount - $this->auction_expenses;
        return $this->davaktiv_amount;
    }

    public function autoCalculate()
    {
        $this->calculateAuctionFee();
        $this->calculateIncomingAmount();
        $this->calculateDavaktivAmount();
    }

    // Scopes
    public function scopeByTuman($query, $tumanId)
    {
        return $query->where('tuman_id', $tumanId);
    }

    public function scopeWithContract($query)
    {
        return $query->where('contract_signed', true);
    }

    public function scopeInstallmentPayment($query)
    {
        return $query->where('payment_type', 'muddatli');
    }

    public function scopeOneTimePayment($query)
    {
        return $query->where('payment_type', 'muddatli_emas');
    }
}
