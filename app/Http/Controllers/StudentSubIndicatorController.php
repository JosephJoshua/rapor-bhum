<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\Student;
use App\Models\SubIndicator;

class StudentSubIndicatorController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(AcademicTerm $academicTerm, Student $student, SubIndicator $subindicator)
    {
        $student->subindicators()->attach($subindicator, [
            'academic_term_id' => $academicTerm->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicTerm $academicTerm, Student $student, SubIndicator $subindicator)
    {
        $student->subindicators()->wherePivot('academic_term_id', $academicTerm->id)->detach($subindicator);
    }
}
