<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeDescriptor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'min_grade', 'max_grade'];
}
