<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'school_class_id'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subindicators()
    {
        return $this->belongsToMany(SubIndicator::class, 'student_sub_indicators');
    }

    public function gradeDescriptors()
    {
        // can do this with a single query, but it's not worth the effort
        $max = GradeDescriptor::max('max_grade');

        return Indicator::all()->map(function ($indicator) use ($max) {
            $count = $this->subindicators()->where('indicator_id', $indicator->id)->count();
            $total = $indicator->subindicators()->count();

            $transformed = $count / $total * $max;

            $gradeDescriptor = GradeDescriptor::where('min_grade', '<=', ceil($transformed))
                ->where('max_grade', '>=', floor($transformed))
                ->first();

            return [
                'grade_descriptor' => $gradeDescriptor,
                'indicator' => $indicator,
            ];
        });
    }
}
