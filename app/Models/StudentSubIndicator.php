<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubIndicator extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'sub_indicator_id'];
}
