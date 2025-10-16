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
        'payment_type',
        'contract_amount',
        'paid_amount',
        'remaining_amount',
        'note',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    const PAYMENT_TYPE_INSTALLMENT = 'muddatli';
    const PAYMENT_TYPE_ONE_TIME = 'muddatsiz';

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            $contract->created_by = auth()->id();
            $contract->remaining_amount = $contract->contract_amount;
        });

        static::updating(function ($contract) {
            $contract->updated_by = auth()->id();
            $contract->remaining_amount = $contract->contract_amount - $contract->paid_amount;
        });
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
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

    public function isMuddatli()
    {
        return $this->payment_type === self::PAYMENT_TYPE_INSTALLMENT;
    }

    public function isMuddatsiz()
    {
        return $this->payment_type === self::PAYMENT_TYPE_ONE_TIME;
    }

    public function getPaymentPercentageAttribute()
    {
        if ($this->contract_amount == 0) return 0;
        return ($this->paid_amount / $this->contract_amount) * 100;
    }

    public function isCompleted()
    {
        return $this->paid_amount >= $this->contract_amount;
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_ACTIVE => 'Фаол',
            self::STATUS_COMPLETED => 'Тўланган',
            self::STATUS_CANCELLED => 'Бекор қилинган',
        ][$this->status] ?? 'Номаълум';
    }

    public function getPaymentTypeLabelAttribute()
    {
        return [
            self::PAYMENT_TYPE_INSTALLMENT => 'Муддатли',
            self::PAYMENT_TYPE_ONE_TIME => 'Муддатсиз',
        ][$this->payment_type] ?? 'Номаълум';
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeMuddatli($query)
    {
        return $query->where('payment_type', self::PAYMENT_TYPE_INSTALLMENT);
    }

    public function scopeMuddatsiz($query)
    {
        return $query->where('payment_type', self::PAYMENT_TYPE_ONE_TIME);
    }
}
