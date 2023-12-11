<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcademicTermController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('AcademicTerm/Index', [
            'data' => fn () =>
                AcademicTerm::orderBy('start_year', 'desc')->orderBy('end_year', 'desc')->orderBy('term', 'desc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('AcademicTerm/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_year' => ['required', 'numeric', 'integer', 'min:2000', 'max:2100'],
            'end_year' => ['required', 'numeric', 'integer', 'min:2000', 'max:2100', 'gte:start_year'],
            'term' => ['required', 'numeric', 'integer', 'in:1,2']
        ]);

        AcademicTerm::create($validated);
        return redirect()->route('academic-terms.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicTerm $academicTerm)
    {
        return Inertia::render('AcademicTerm/Edit', [
            'data' => $academicTerm,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicTerm $academicTerm)
    {
        $validated = $request->validate([
            'start_year' => ['required', 'numeric', 'integer', 'min:2000', 'max:2100'],
            'end_year' => ['required', 'numeric', 'integer', 'min:2000', 'max:2100', 'gte:start_year'],
            'term' => ['required', 'numeric', 'integer', 'in:1,2']
        ]);

        $academicTerm->start_year = $validated['start_year'];
        $academicTerm->end_year = $validated['end_year'];
        $academicTerm->term = $validated['term'];

        $academicTerm->save();
        return redirect()->route('academic-terms.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicTerm $academicTerm)
    {
        AcademicTerm::destroy($academicTerm->id);
    }
}
