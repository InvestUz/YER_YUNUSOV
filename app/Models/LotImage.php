<?php
// app/Models/LotImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LotImage extends Model
{
    protected $fillable = [
        'lot_id',
        'image_path',
        'order',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function getUrlAttribute()
    {
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }
}