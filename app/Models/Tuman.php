<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuman extends Model
{

    protected $table = 'tumans';
    protected $fillable = ['region_id', 'name_uz', 'name_ru'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function mahallas()
    {
        return $this->hasMany(Mahalla::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
