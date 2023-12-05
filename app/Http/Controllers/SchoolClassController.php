<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Unit;
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
            'name' => ['required', 'max:255'],
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
            'name' => ['required', 'max:255'],
        ]);

        $schoolClass->name = $validated['name'];
        $schoolClass->save();

        return redirect()->route('units.show', ['unit' => $unit]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit, SchoolClass $schoolClass)
    {
        SchoolClass::destroy($schoolClass->id);
    }
}
