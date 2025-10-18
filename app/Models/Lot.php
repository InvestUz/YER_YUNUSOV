<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;

class Lot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'lot_number',
        'tuman_id',
        'mahalla_id',
        'address',
        'unique_number',
        'zone',
        'latitude',
        'longitude',
        'location_url',
        'master_plan_zone',
        'yangi_uzbekiston',
        'land_area',
        'object_type',
        'object_type_ru',
        'construction_area',
        'investment_amount',
        'initial_price',
        'auction_date',
        'sold_price',
        'winner_type',
        'winner_name',
        'winner_phone',
        'payment_type',
        'basis',
        'auction_type',
        'lot_status',
        'contract_signed',
        'contract_date',
        'contract_number',
        'paid_amount',
        'transferred_amount',
        'discount',
        'auction_fee',
        'incoming_amount',
        'davaktiv_amount',
        'auction_expenses',
        'views_count',
        'likes_count'
    ];

    protected $casts = [
        'yangi_uzbekiston' => 'boolean',
        'contract_signed' => 'boolean',
        'auction_date' => 'date',
        'contract_date' => 'date',
        'land_area' => 'decimal:2',
        'construction_area' => 'decimal:2',
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

    // Extract coordinates from Google Maps URL
    public function extractCoordinatesFromUrl()
    {
        if (!$this->location_url || ($this->latitude && $this->longitude)) {
            return;
        }

        // Pattern 1: @lat,lng format
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $this->location_url, $matches)) {
            $this->latitude = $matches[1];
            $this->longitude = $matches[2];
            $this->save();
            return;
        }

        // Pattern 2: 3d parameter format
        if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $this->location_url, $matches)) {
            $this->latitude = $matches[1];
            $this->longitude = $matches[2];
            $this->save();
            return;
        }

        // Pattern 3: Degrees format like in your URL
        if (preg_match('/(\d+)%C2%B0(\d+)\'([\d.]+)%22N\+(\d+)%C2%B0(\d+)\'([\d.]+)%22E/', $this->location_url, $matches)) {
            // Convert DMS to decimal
            $lat = $matches[1] + ($matches[2] / 60) + ($matches[3] / 3600);
            $lng = $matches[4] + ($matches[5] / 60) + ($matches[6] / 3600);

            $this->latitude = $lat;
            $this->longitude = $lng;
            $this->save();
            return;
        }

        // Pattern 4: Direct parameters
        if (preg_match('/3d(-?\d+\.\d+).*4d(-?\d+\.\d+)/', $this->location_url, $matches)) {
            $this->latitude = $matches[1];
            $this->longitude = $matches[2];
            $this->save();
        }
    }

    // Get map embed URL
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

    // Get primary image or default
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

        // Return a professional placeholder
        return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600"%3E%3Crect width="800" height="600" fill="%23e5e7eb"/%3E%3Cg transform="translate(400,300)"%3E%3Cpath d="M-80-60h160v120h-160z" fill="%239ca3af" opacity="0.3"/%3E%3Ccircle cx="-40" cy="-20" r="15" fill="%239ca3af" opacity="0.5"/%3E%3Cpath d="M-80 60l60-80 40 50 60-80 60 110h-220z" fill="%239ca3af" opacity="0.4"/%3E%3C/g%3E%3Ctext x="400" y="340" text-anchor="middle" font-family="Arial" font-size="16" fill="%236b7280"%3EРасм мавжуд эмас%3C/text%3E%3C/svg%3E';
    }

    // Get all images or default
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

    // Increment views
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Toggle like
    public function toggleLike()
    {
        // You can implement user-specific likes with a pivot table
        // For now, just increment/decrement
        $this->increment('likes_count');
    }

    // Automatic calculations
    public function calculateAuctionFee()
    {
        if ($this->sold_price) {
            $this->auction_fee = $this->sold_price * 0.01;
        }
        return $this->auction_fee;
    }

    public function calculateIncomingAmount()
    {
        $this->incoming_amount = $this->transferred_amount - $this->discount - $this->auction_fee;
        return $this->incoming_amount;
    }

    public function calculateDavaktivAmount()
    {
        $this->davaktiv_amount = $this->incoming_amount - $this->auction_expenses;
        return $this->davaktiv_amount;
    }

    public function autoCalculate()
    {
        $this->calculateAuctionFee();
        $this->calculateIncomingAmount();
        $this->calculateDavaktivAmount();
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
        return $query->where('payment_type', 'muddatsiz');
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

    // Helper method to get unique viewers
    public function getUniqueViewersCountAttribute()
    {
        return $this->views()->distinct('ip_address')->count('ip_address');
    }

    // Helper method to get authenticated viewers
    public function getAuthenticatedViewersCountAttribute()
    {
        return $this->views()->whereNotNull('user_id')->distinct('user_id')->count('user_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    // Lot ning shartnoma borligini tekshirish
    public function hasContract()
    {
        return $this->contract()->exists();
    }

    // Lot ning shartnoma raqamini olish
    public function getContractNumberAttribute()
    {
        return $this->contract?->contract_number ?? '-';
    }
}
