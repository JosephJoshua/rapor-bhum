<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('SchoolClass/Index', [
            'data' => fn () =>
                SchoolClass::with('unit')
                    ->with('teacher')
                    ->get()
                    ->sortBy('unit.name')
                    ->sortBy('name'),
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
    public function show(Unit $unit, SchoolClass $schoolClass)
    {
        return Inertia::render('SchoolClass/Show', [
            'data' => $schoolClass,
            'unit' => $unit,
            'students' => fn () => $schoolClass->students()->get(),
            'shouldGoBackToSchoolClassIndex' => Auth::user()->role !== 'admin',
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
