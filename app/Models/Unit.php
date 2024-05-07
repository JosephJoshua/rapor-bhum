<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'head'];

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function indicators()
    {
        return $this->belongsToMany(Indicator::class, 'unit_indicators');
    }
}