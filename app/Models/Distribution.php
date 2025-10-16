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
        'allocated_amount' => 'decimal:2',
        'distribution_date' => 'date',
    ];

    const CATEGORY_TASHKENT_BUDGET = 'tashkent_budget';
    const CATEGORY_DEVELOPMENT_FUND = 'development_fund';
    const CATEGORY_SHAYXONTOXUR_BUDGET = 'shayxontoxur_budget';
    const CATEGORY_NEW_UZBEKISTAN = 'new_uzbekistan';
    const CATEGORY_INDUSTRIAL_PARK = 'industrial_park';
    const CATEGORY_KSZ_DIRECTORATE = 'ksz_directorate';
    const CATEGORY_TASHKENT_CITY = 'tashkent_city';
    const CATEGORY_DISTRICT_BUDGET = 'district_budget';

    const STATUS_PENDING = 'pending';
    const STATUS_DISTRIBUTED = 'distributed';
    const STATUS_CANCELLED = 'cancelled';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($distribution) {
            $distribution->created_by = auth()->id();
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

    public static function categories()
    {
        return [
            self::CATEGORY_TASHKENT_BUDGET => 'Тошкент шаҳар бюджети',
            self::CATEGORY_DEVELOPMENT_FUND => 'Жамғарма',
            self::CATEGORY_SHAYXONTOXUR_BUDGET => 'Шайхонтоҳур тумани бюжетига',
            self::CATEGORY_NEW_UZBEKISTAN => 'Янги Ўзбекистон дирекцияссига',
            self::CATEGORY_INDUSTRIAL_PARK => 'Янгиҳаёт индустриал технопакига',
            self::CATEGORY_KSZ_DIRECTORATE => 'КСЗ дирекцияларига',
            self::CATEGORY_TASHKENT_CITY => 'Тошкент сити дирекцияси',
            self::CATEGORY_DISTRICT_BUDGET => 'Туманлар бюжетига',
        ];
    }

    public function getCategoryLabelAttribute()
    {
        return self::categories()[$this->category] ?? 'Номаълум';
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeDistributed($query)
    {
        return $query->where('status', self::STATUS_DISTRIBUTED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public static function validateTotalAmount($contractId, $paymentScheduleId, $distributions)
    {
        $paymentSchedule = PaymentSchedule::findOrFail($paymentScheduleId);
        $totalDistributed = collect($distributions)->sum('allocated_amount');

        if ($totalDistributed > $paymentSchedule->actual_amount) {
            throw new \Exception("Тақсимланаётган сумма ({$totalDistributed}) тўловдан ({$paymentSchedule->actual_amount}) кўп бўлиши мумкин эмас!");
        }

        return true;
    }
}
