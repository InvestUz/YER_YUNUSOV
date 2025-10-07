<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'lot_id',
        'year',
        'month',
        'payment_date',
        'planned_amount',
        'actual_amount',
        'difference',
        'payment_frequency',
        'is_additional_agreement',
        'note'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'is_additional_agreement' => 'boolean',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    // Auto-calculate difference
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payment) {
            $payment->difference = $payment->actual_amount - $payment->planned_amount;
        });
    }

    // Calculate percentage paid
    public function getPercentagePaidAttribute()
    {
        if ($this->planned_amount == 0) return 0;
        return ($this->actual_amount / $this->planned_amount) * 100;
    }

    // Check if overdue
    public function getIsOverdueAttribute()
    {
        return $this->payment_date < now() && $this->actual_amount < $this->planned_amount;
    }
}
