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

    // All Distribution Categories
    const CATEGORY_CITY_BUDGET = 'city_budget';
    const CATEGORY_DEVELOPMENT_FUND = 'development_fund';
    const CATEGORY_SHAYKHONTOHUR_BUDGET = 'shaykhontohur_budget';
    const CATEGORY_NEW_UZBEKISTAN = 'new_uzbekistan';
    const CATEGORY_YANGIKHAYOT_TECHNOPARK = 'yangikhayot_technopark';
    const CATEGORY_KSZ_DIRECTORATES = 'ksz_directorates';
    const CATEGORY_TASHKENT_CITY_DIRECTORATE = 'tashkent_city_directorate';
    const CATEGORY_DISTRICT_BUDGETS = 'district_budgets';

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

    public static function getCategories()
    {
        return [
            self::CATEGORY_CITY_BUDGET => 'Тошкент шаҳар бюджети',
            self::CATEGORY_DEVELOPMENT_FUND => 'Жамғармага',
            self::CATEGORY_SHAYKHONTOHUR_BUDGET => 'Шайхонтоҳур тумани бюжетига',
            self::CATEGORY_NEW_UZBEKISTAN => 'Янги Ўзбекистон дирекцияссига',
            self::CATEGORY_YANGIKHAYOT_TECHNOPARK => 'Янгиҳаёт индустриал технопакига',
            self::CATEGORY_KSZ_DIRECTORATES => 'КСЗ дирекцияларига',
            self::CATEGORY_TASHKENT_CITY_DIRECTORATE => 'Тошкент сити дирекцияси',
            self::CATEGORY_DISTRICT_BUDGETS => 'Туманлар бюжетига',
        ];
    }

    public function getCategoryLabelAttribute()
    {
        return self::getCategories()[$this->category] ?? $this->category;
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
