<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name_uz', 'name_ru'];

    public function tumans()
    {
        return $this->hasMany(Tuman::class);
    }
}
