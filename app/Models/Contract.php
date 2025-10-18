<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lot_id',
        'contract_number',
        'contract_date',
        'contract_amount',
        'initial_paid_amount',
        'initial_payment_date',
        'payment_type',
        'buyer_name',
        'buyer_phone',
        'buyer_inn',
        'status',
        'note',
        'paid_amount',
        'remaining_amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'initial_payment_date' => 'date',
        'contract_amount' => 'decimal:2',
        'initial_paid_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            $contract->created_by = auth()->id();
        });

        static::updating(function ($contract) {
            $contract->updated_by = auth()->id();
        });
    }

    // Relationships
    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class)->orderBy('payment_number');
    }

    public function additionalAgreements()
    {
        return $this->hasMany(AdditionalAgreement::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getPaymentPercentageAttribute()
    {
        if ($this->contract_amount == 0) return 0;
        return ($this->paid_amount / $this->contract_amount) * 100;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->contract_amount - $this->paid_amount;
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_ACTIVE => 'Фаол',
            self::STATUS_COMPLETED => 'Тўланган',
            self::STATUS_CANCELLED => 'Бекор қилинган',
        ][$this->status] ?? 'Номаълум';
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_ACTIVE => 'blue',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
        ][$this->status] ?? 'gray';
    }

    // Methods
    public function updatePaidAmount()
    {
        $totalPaid = $this->paymentSchedules()->sum('actual_amount');
        $this->paid_amount = $totalPaid;
        $this->remaining_amount = $this->contract_amount - $totalPaid;

        if ($this->remaining_amount <= 0 && $this->status !== self::STATUS_CANCELLED) {
            $this->status = self::STATUS_COMPLETED;
        }

        $this->save();
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeInstallment($query)
    {
        return $query->where('payment_type', 'muddatli');
    }

    public function scopeOneTime($query)
    {
        return $query->where('payment_type', 'muddatsiz');
    }
}
