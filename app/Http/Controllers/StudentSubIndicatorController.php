<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SubIndicator;

class StudentSubIndicatorController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Student $student, SubIndicator $subindicator)
    {
        $student->subindicators()->attach($subindicator);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student, SubIndicator $subindicator)
    {
        $student->subindicators()->detach($subindicator);
    }
}
