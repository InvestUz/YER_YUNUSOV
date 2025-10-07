<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahalla extends Model
{
    protected $fillable = ['tuman_id', 'name', 'name_ru'];

    public function tuman()
    {
        return $this->belongsTo(Tuman::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
