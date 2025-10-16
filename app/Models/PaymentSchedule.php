<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'contract_id',
        'additional_agreement_id',
        'payment_number',
        'planned_date',
        'deadline_date',
        'planned_amount',
        'actual_date',
        'actual_amount',
        'difference',
        'status',
        'note',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'planned_date' => 'date',
        'deadline_date' => 'date',
        'actual_date' => 'date',
        'planned_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            $schedule->created_by = auth()->id();
        });

        static::updating(function ($schedule) {
            $schedule->updated_by = auth()->id();
            $schedule->difference = $schedule->actual_amount - $schedule->planned_amount;

            if ($schedule->actual_amount >= $schedule->planned_amount) {
                $schedule->status = self::STATUS_PAID;
            } elseif ($schedule->actual_amount > 0) {
                $schedule->status = self::STATUS_PARTIAL;
            } elseif ($schedule->deadline_date < now() && $schedule->actual_amount == 0) {
                $schedule->status = self::STATUS_OVERDUE;
            }
        });
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function additionalAgreement()
    {
        return $this->belongsTo(AdditionalAgreement::class);
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

    public function getPercentagePaidAttribute()
    {
        if ($this->planned_amount == 0) return 0;
        return ($this->actual_amount / $this->planned_amount) * 100;
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isOverdue()
    {
        return $this->status === self::STATUS_OVERDUE ||
               ($this->deadline_date < now() && $this->actual_amount < $this->planned_amount);
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING => 'Кутилмоқда',
            self::STATUS_PARTIAL => 'Қисман тўланган',
            self::STATUS_PAID => 'Тўланган',
            self::STATUS_OVERDUE => 'Муддати ўтган',
        ][$this->status] ?? 'Номаълум';
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_PARTIAL => 'blue',
            self::STATUS_PAID => 'green',
            self::STATUS_OVERDUE => 'red',
        ][$this->status] ?? 'gray';
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline_date', '<', now())
                    ->where('status', '!=', self::STATUS_PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
}
