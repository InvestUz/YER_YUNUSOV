<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Lot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lot_number',
        'tuman_id',
        'mahalla_id',
        'address',
        'unique_number',
        'land_area',
        'zone',
        'master_plan_zone',
        'yangi_uzbekiston',
        'auction_date',
        'sold_price',
        'initial_price',
        'winner_name',
        'winner_type',
        'huquqiy_subyekt',
        'winner_phone',
        'basis',
        'auction_type',
        'object_type',
        'latitude',
        'longitude',
        'location_url',
        'contract_signed',
        'paid_amount',
        'transferred_amount',
        'discount',
        'payment_type',
        'auction_fee',
        'auction_expenses',
        'lot_status',

    ];

    protected $casts = [
        'yangi_uzbekiston' => 'boolean',
        'contract_signed' => 'boolean',
        'auction_date' => 'date',
        'contract_date' => 'date',

        // Area measurements - 4 decimal places for precision
        'land_area' => 'decimal:4',           // Handles 0.0019 to 9999.9999 hectares
        'construction_area' => 'decimal:4',    // Handles large square meter values

        // Financial values - 2 decimal places (standard for currency)
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

        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];
    // Discount cutoff date constant
    const DISCOUNT_CUTOFF_DATE = '2024-09-10';
    const DISCOUNT_PERCENTAGE = 20;


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

    public function images()
    {
        return $this->hasMany(LotImage::class)->orderBy('order');
    }

    public function primaryImage()
    {
        return $this->hasOne(LotImage::class)->where('is_primary', true);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function hasContract()
    {
        return $this->contract()->exists();
    }

    public function getContractNumberAttribute()
    {
        return $this->contract?->contract_number ?? '-';
    }

    /**
     * Check if lot qualifies for discount (muddatsiz + after cutoff date)
     */
    public function qualifiesForDiscount()
    {
        if (!in_array($this->payment_type, ['muddatsiz', 'muddatli_emas'])) {
            return false;
        }

        if (!$this->auction_date) {
            return false;
        }

        // Strictly AFTER September 10, 2024
        return $this->auction_date->gt(Carbon::parse(self::DISCOUNT_CUTOFF_DATE));
    }

    /**
     * Calculate discount amount based on auction date and payment type
     */
    public function calculateDiscount()
    {
        if (!$this->qualifiesForDiscount() || !$this->paid_amount) {
            $this->discount = 0;
            return 0;
        }

        // 20% discount for muddatsiz AFTER 10.09.2024
        $this->discount = $this->paid_amount * (self::DISCOUNT_PERCENTAGE / 100);
        return $this->discount;
    }

    /**
     * Calculate auction fee (1% of sold price)
     */
    public function calculateAuctionFee()
    {
        if ($this->sold_price) {
            $this->auction_fee = $this->sold_price * 0.01;
        } else {
            $this->auction_fee = 0;
        }
        return $this->auction_fee;
    }

    /**
     * Calculate incoming amount (after discount and auction fee)
     * CRITICAL: For muddatsiz with transferred_amount = 0, use paid_amount
     */
    public function calculateIncomingAmount()
    {
        // For muddatsiz/muddatli_emas, if transferred_amount is 0, use paid_amount
        $baseAmount = $this->transferred_amount;

        if (in_array($this->payment_type, ['muddatsiz', 'muddatli_emas']) && $this->transferred_amount == 0) {
            $baseAmount = $this->paid_amount;
        }

        // Calculate incoming: base - discount - auction_fee
        $this->incoming_amount = max(0, $baseAmount - $this->discount - ($this->auction_fee ?? 0));

        return $this->incoming_amount;
    }

    /**
     * Calculate davaktiv amount
     */
    public function calculateDavaktivAmount()
    {
        $this->davaktiv_amount = max(0, $this->incoming_amount - ($this->auction_expenses ?? 0));
        return $this->davaktiv_amount;
    }

    /**
     * Get the amount that should be distributed
     * CRITICAL: For post-10.09.2024 muddatsiz, incoming_amount is already after 20% discount
     * So we distribute 100% of incoming_amount
     */
    public function getDistributableAmountAttribute()
    {
        return round($this->incoming_amount, 0);
    }

    /**
     * Get remaining amount to be distributed
     */
    public function getRemainingDistributableAmountAttribute()
    {
        $totalDistributed = 0;

        if ($this->contract && $this->contract->distributions) {
            $totalDistributed = $this->contract->distributions->sum('allocated_amount');
        }

        return round($this->distributable_amount - $totalDistributed, 0);
    }

    /**
     * Check if distribution is complete
     */
    public function isDistributionComplete()
    {
        return $this->remaining_distributable_amount <= 0.01; // Allow small rounding difference
    }

    /**
     * Get distribution progress percentage
     */
    public function getDistributionProgressAttribute()
    {
        if ($this->distributable_amount <= 0) {
            return 0;
        }

        $totalDistributed = 0;
        if ($this->contract && $this->contract->distributions) {
            $totalDistributed = $this->contract->distributions->sum('allocated_amount');
        }

        return min(100, ($totalDistributed / $this->distributable_amount) * 100);
    }

    /**
     * Automatic calculations - CALL THIS AFTER ANY PAYMENT UPDATE
     */
    public function autoCalculate()
    {
        // Calculate in correct order
        $this->calculateAuctionFee();      // 1. Calculate 1% auction fee
        $this->calculateDiscount();        // 2. Calculate 20% discount (if applicable)
        $this->calculateIncomingAmount();  // 3. Calculate incoming after discount & fee
        $this->calculateDavaktivAmount();  // 4. Calculate davaktiv after expenses
    }

    // Extract coordinates from Google Maps URL
    public function extractCoordinatesFromUrl()
    {
        if (!$this->location_url || ($this->latitude && $this->longitude)) {
            return;
        }

        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $this->location_url, $matches)) {
            $this->latitude = $matches[1];
            $this->longitude = $matches[2];
            $this->save();
            return;
        }

        if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $this->location_url, $matches)) {
            $this->latitude = $matches[1];
            $this->longitude = $matches[2];
            $this->save();
            return;
        }

        if (preg_match('/(\d+)%C2%B0(\d+)\'([\d.]+)%22N\+(\d+)%C2%B0(\d+)\'([\d.]+)%22E/', $this->location_url, $matches)) {
            $lat = $matches[1] + ($matches[2] / 60) + ($matches[3] / 3600);
            $lng = $matches[4] + ($matches[5] / 60) + ($matches[6] / 3600);
            $this->latitude = $lat;
            $this->longitude = $lng;
            $this->save();
            return;
        }

        if (preg_match('/3d(-?\d+\.\d+).*4d(-?\d+\.\d+)/', $this->location_url, $matches)) {
            $this->latitude = $matches[1];
            $this->longitude = $matches[2];
            $this->save();
        }
    }

    public function getMapEmbedUrlAttribute()
    {
        $this->extractCoordinatesFromUrl();

        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $lat = $this->latitude;
        $lng = $this->longitude;

        return "https://www.openstreetmap.org/export/embed.html?bbox=" .
            ($lng - 0.005) . "%2C" . ($lat - 0.005) . "%2C" .
            ($lng + 0.005) . "%2C" . ($lat + 0.005) .
            "&layer=mapnik&marker={$lat}%2C{$lng}";
    }

    public function getPrimaryImageUrlAttribute()
    {
        $primaryImage = $this->primaryImage;

        if ($primaryImage) {
            return $primaryImage->url;
        }

        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->url;
        }

        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600"%3E%3Crect width="800" height="600" fill="%23e5e7eb"/%3E%3Cg transform="translate(400,300)"%3E%3Cpath d="M-80-60h160v120h-160z" fill="%239ca3af" opacity="0.3"/%3E%3Ccircle cx="-40" cy="-20" r="15" fill="%239ca3af" opacity="0.5"/%3E%3Cpath d="M-80 60l60-80 40 50 60-80 60 110h-220z" fill="%239ca3af" opacity="0.4"/%3E%3C/g%3E%3Ctext x="400" y="340" text-anchor="middle" font-family="Arial" font-size="16" fill="%236b7280"%3EРасм мавжуд эмас%3C/text%3E%3C/svg%3E';
    }

    public function getAllImagesAttribute()
    {
        $images = $this->images;

        if ($images->isEmpty()) {
            return collect([
                (object)['url' => $this->primary_image_url, 'is_placeholder' => true]
            ]);
        }

        return $images->map(function ($img) {
            $img->is_placeholder = false;
            return $img;
        });
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function toggleLike()
    {
        $this->increment('likes_count');
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
        return $query->where(function ($q) {
            $q->where('payment_type', 'muddatsiz')
                ->orWhereNull('payment_type'); // Include NULL as one-time payment
        });
    }
    public function views()
    {
        return $this->hasMany(LotView::class);
    }

    public function likes()
    {
        return $this->hasMany(LotLike::class);
    }

    public function messages()
    {
        return $this->hasMany(LotMessage::class);
    }

    public function getUniqueViewersCountAttribute()
    {
        return $this->views()->distinct('ip_address')->count('ip_address');
    }

    public function getAuthenticatedViewersCountAttribute()
    {
        return $this->views()->whereNotNull('user_id')->distinct('user_id')->count('user_id');
    }
}
