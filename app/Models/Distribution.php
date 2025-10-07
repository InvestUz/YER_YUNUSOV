<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'lot_id', 'category', 'allocated_amount', 'remaining_amount'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    const CATEGORY_LOCAL_BUDGET = 'local_budget';
    const CATEGORY_DEVELOPMENT_FUND = 'development_fund';
    const CATEGORY_NEW_UZBEKISTAN = 'new_uzbekistan';
    const CATEGORY_DISTRICT_AUTHORITY = 'district_authority';

    public static function categories()
    {
        return [
            self::CATEGORY_LOCAL_BUDGET => 'Махаллий бюджет',
            self::CATEGORY_DEVELOPMENT_FUND => 'Тошкент шаҳрини ривожлантириш жамғармаси',
            self::CATEGORY_NEW_UZBEKISTAN => 'Янги Ўзбекистон дирекцияси',
            self::CATEGORY_DISTRICT_AUTHORITY => 'Туман ҳокимияти',
        ];
    }
}
