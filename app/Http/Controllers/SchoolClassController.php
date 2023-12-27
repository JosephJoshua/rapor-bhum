<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\Indicator;
use App\Models\SchoolClass;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SchoolClassController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SchoolClass::class, 'school_class');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        return Inertia::render('SchoolClass/Index', [
            'data' => function () use ($user, $isAdmin) {
                return SchoolClass::with('unit')
                    ->with('teacher')
                    ->when(!$isAdmin, function ($query) use ($user) {
                        $query->where('teacher_user_id', $user->id);
                    })
                    ->get()
                    ->sortBy('unit.name')
                    ->sortBy('name');
            }
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Unit $unit)
    {
        return Inertia::render('SchoolClass/Create', [
            'unit' => $unit,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $unit->classes()->create($validated);
        return redirect()->route('units.show', ['unit' => $unit]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Unit $unit, SchoolClass $schoolClass)
    {
        $request->query('academic_term_id') === null
            ? $academicTerm = null
            : $academicTerm = AcademicTerm::find($request->query('academic_term_id'));

        return Inertia::render('SchoolClass/Show', [
            'data' => $schoolClass,
            'unit' => $unit,
            'students' => fn () =>
                $schoolClass
                    ->students()
                    ->with([
                        'studentSubIndicators' => function ($query) use ($academicTerm) {
                            if ($academicTerm === null) return;
                            $query->where('academic_term_id', '=', $academicTerm->id);
                        },
                        'studentSubIndicators.subIndicator',
                    ])
                    ->get()
                    ->each(function ($student) use ($academicTerm) {
                        $student->subindicators = $student
                            ->studentSubIndicators
                            ->map(fn ($el) => $el->subIndicator);

                        $student->grade_descriptors =
                            $academicTerm === null
                                ? []
                                : $student->gradeDescriptors($academicTerm);
                    }),
            'indicators' => fn () =>
                Indicator::with('subindicators')
                    ->orderBy('name', 'asc')
                    ->get(),
            'academic_terms' => fn () =>
                AcademicTerm::orderBy('start_year', 'desc')
                    ->orderBy('end_year', 'desc')
                    ->orderBy('term', 'desc')
                    ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit, SchoolClass $schoolClass)
    {
        return Inertia::render('SchoolClass/Edit', [
            'unit' => $unit,
            'data' => $schoolClass,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit, SchoolClass $schoolClass)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $schoolClass->name = $validated['name'];
        $schoolClass->save();

        return redirect()->route('units.show', ['unit' => $unit]);
    }

    public function updateTeacher(Request $request, Unit $unit, SchoolClass $schoolClass)
    {
        $validated = $request->validate([
            'teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        if (!isset($validated['teacher_id']))
        {
            $schoolClass->teacher_user_id = null;
            $schoolClass->save();

            return;
        }

        if (User::find($validated['teacher_id'])->role !== 'teacher')
        {
            return redirect()->back()->withErrors(['teacher_id' => 'The selected teacher is not a teacher.']);
        }

        $schoolClass->teacher_user_id = $validated['teacher_id'];
        $schoolClass->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit, SchoolClass $schoolClass)
    {
        SchoolClass::destroy($schoolClass->id);
    }
}
