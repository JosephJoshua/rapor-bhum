<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubIndicator extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'sub_indicator_id', 'academic_term_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subIndicator()
    {
        return $this->belongsTo(SubIndicator::class);
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }
}
