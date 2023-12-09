<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function subindicators()
    {
        return $this->hasMany(SubIndicator::class)->orderBy('name', 'asc');
    }
}
