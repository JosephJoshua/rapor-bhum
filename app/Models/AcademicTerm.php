<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $fillable = ['start_year', 'end_year', 'term'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_sub_indicators');
    }

    public function subindicators()
    {
        return $this->belongsToMany(SubIndicator::class, 'student_sub_indicators');
    }

    public function studentSubIndicators()
    {
        return $this->hasMany(StudentSubIndicator::class);
    }
}
