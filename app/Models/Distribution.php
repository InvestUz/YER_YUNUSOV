<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'contract_id',
        'payment_schedule_id',
        'additional_agreement_id',
        'category',
        'allocated_amount',
        'distribution_date',
        'status',
        'note',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'allocated_amount' => 'decimal:2',
    ];

    const CATEGORY_LOCAL_BUDGET = 'local_budget';
    const CATEGORY_DEVELOPMENT_FUND = 'development_fund';
    const CATEGORY_NEW_UZBEKISTAN = 'new_uzbekistan';
    const CATEGORY_DISTRICT_AUTHORITY = 'district_authority';

    const STATUS_PENDING = 'pending';
    const STATUS_DISTRIBUTED = 'distributed';
    const STATUS_CANCELLED = 'cancelled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($distribution) {
            $distribution->created_by = auth()->id();
            $distribution->status = $distribution->status ?? self::STATUS_PENDING;
        });

        static::updating(function ($distribution) {
            $distribution->updated_by = auth()->id();
        });
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function paymentSchedule()
    {
        return $this->belongsTo(PaymentSchedule::class);
    }

    public function additionalAgreement()
    {
        return $this->belongsTo(AdditionalAgreement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getCategoryLabelAttribute()
    {
        return [
            self::CATEGORY_LOCAL_BUDGET => 'Маҳаллий бюджет',
            self::CATEGORY_DEVELOPMENT_FUND => 'Тошкент шаҳрини ривожлантириш жамғармаси',
            self::CATEGORY_NEW_UZBEKISTAN => 'Янги Ўзбекистон',
            self::CATEGORY_DISTRICT_AUTHORITY => 'Туман ҳокимияти',
        ][$this->category] ?? $this->category;
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => 'Кутилмоқда',
            self::STATUS_DISTRIBUTED => 'Тақсимланган',
            self::STATUS_CANCELLED => 'Бекор қилинган',
        ][$this->status] ?? 'Номаълум';
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_DISTRIBUTED => 'green',
            self::STATUS_CANCELLED => 'red',
        ][$this->status] ?? 'gray';
    }

    public function scopeDistributed($query)
    {
        return $query->where('status', self::STATUS_DISTRIBUTED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
