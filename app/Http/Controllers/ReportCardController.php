<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\GradeDescriptor;
use App\Models\Indicator;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportCardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        $academicTermId = $request->query('academic_term_id');
        $studentId = $request->query('student_id');

        $academicTermId === null
            ? $academicTerm = null
            : $academicTerm = AcademicTerm::find($academicTermId);

        $student = null;

        if ($studentId !== null) {
            $student = Student::with('schoolClass', 'schoolClass.unit')
                ->when(!$isAdmin, function ($query) use ($user) {
                    $query->whereRelation('schoolClass', 'teacher_user_id', $user->id);
                })
                ->find($studentId);

            if ($student !== null) {
                $student->grade_descriptors =
                    $academicTerm === null
                        ? []
                        : $student->gradeDescriptors($academicTerm);
            }
        }

        return Inertia::render('ReportCard/Show', [
            'data' => $student,
            'academic_term' => $academicTerm,
            'academic_terms' => fn () =>
                AcademicTerm::orderBy('start_year', 'desc')
                    ->orderBy('end_year', 'desc')
                    ->orderBy('term', 'desc')
                    ->get(),
            'students' => function () use ($isAdmin, $user) {
                if ($isAdmin) {
                    return Student::orderBy('name')->get();
                }

                return Student::whereRelation('schoolClass', 'teacher_user_id', $user->id)
                    ->orderBy('students.name')
                    ->get();
            },
            'indicators' => fn () =>
                Indicator::with('subindicators')
                    ->whereHas('units', function ($query) use ($student) {
                        if ($student == null) return;
                        $query->where('units.id', $student->schoolClass->unit->id);
                    })
                    ->orderBy('name', 'asc')
                    ->get(),
            'grade_descriptors' => fn () =>
                GradeDescriptor::orderBy('min_grade', 'asc')
                    ->orderBy('max_grade', 'asc')
                    ->get(),
            'max_grade' => fn () => GradeDescriptor::max('max_grade') ?? 0,
        ]);
    }
}