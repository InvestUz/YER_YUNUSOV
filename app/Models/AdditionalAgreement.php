<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalAgreement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_id',
        'agreement_number',
        'agreement_date',
        'new_amount',
        'reason',
        'note',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'new_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agreement) {
            $agreement->created_by = auth()->id();
        });

        static::updating(function ($agreement) {
            $agreement->updated_by = auth()->id();
        });
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class);
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
}
