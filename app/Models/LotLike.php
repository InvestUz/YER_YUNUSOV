<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotLike extends Model
{
    protected $fillable = [
        'lot_id',
        'user_id',
        'ip_address',
        'liked_at'
    ];

    protected $casts = [
        'liked_at' => 'datetime',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a lot is liked by user or IP
     */
    public static function isLikedBy($lotId, $userId = null, $ipAddress = null)
    {
        $query = static::where('lot_id', $lotId);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($ipAddress) {
            $query->where('ip_address', $ipAddress)->whereNull('user_id');
        }

        return $query->exists();
    }

    /**
     * Toggle like
     */
    public static function toggleLike($lotId, $userId = null, $ipAddress = null)
    {
        $query = static::where('lot_id', $lotId);

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($ipAddress) {
            $query->where('ip_address', $ipAddress)->whereNull('user_id');
        }

        $like = $query->first();

        if ($like) {
            $like->delete();
            return ['liked' => false, 'deleted' => true];
        } else {
            static::create([
                'lot_id' => $lotId,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'liked_at' => now()
            ]);
            return ['liked' => true, 'created' => true];
        }
    }
}
