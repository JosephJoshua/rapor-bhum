<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitIndicator extends Model
{
    use HasFactory;

    protected $fillable = ['unit_id', 'indicator_id'];
}