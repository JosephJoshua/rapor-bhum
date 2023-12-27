<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Student::class, 'student');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Unit $unit, SchoolClass $schoolClass)
    {
        return Inertia::render('Student/Create', [
            'unit' => $unit,
            'schoolClass' => $schoolClass,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Unit $unit, SchoolClass $schoolClass)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $schoolClass->students()->create($validated);
        return redirect()->route('units.school-classes.show', ['unit' => $unit, 'school_class' => $schoolClass]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit, SchoolClass $schoolClass, Student $student)
    {
        return Inertia::render('Student/Edit', [
            'unit' => $unit,
            'schoolClass' => $schoolClass,
            'data' => $student,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit, SchoolClass $schoolClass, Student $student)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $student->name = $validated['name'];
        $student->save();

        return redirect()->route('units.school-classes.show', ['unit' => $unit, 'school_class' => $schoolClass]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit, SchoolClass $schoolClass, Student $student)
    {
        Student::destroy($student->id);
    }
}
