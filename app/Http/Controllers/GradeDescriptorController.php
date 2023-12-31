<?php

namespace App\Http\Controllers;

use App\Models\GradeDescriptor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GradeDescriptorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('GradeDescriptor/Index', [
            'data' => fn () =>
                GradeDescriptor::orderBy('min_grade', 'asc')
                    ->orderBy('max_grade', 'asc')
                    ->get(),
            'max_grade' => fn () => GradeDescriptor::max('max_grade') ?? 0,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('GradeDescriptor/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:grade_descriptors'],
            'code' => ['required', 'string', 'max:10', 'unique:grade_descriptors'],
            'min_grade' => ['required', 'integer', 'min:0', 'unique:grade_descriptors'],
            'max_grade' => ['required', 'integer', 'min:0', 'gte:min_grade'],
            'description_prefix' => ['required', 'string', 'max:255'],
        ]);

        GradeDescriptor::create($validated);
        return redirect()->route('grade-descriptors.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradeDescriptor $gradeDescriptor)
    {
        return Inertia::render('GradeDescriptor/Edit', [
            'data' => $gradeDescriptor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GradeDescriptor $gradeDescriptor)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:grade_descriptors,name,' . $gradeDescriptor->id],
            'code' => ['required', 'string', 'max:10', 'unique:grade_descriptors,code,' . $gradeDescriptor->id],
            'min_grade' => ['required', 'integer', 'min:0', 'unique:grade_descriptors,min_grade,' . $gradeDescriptor->id],
            'max_grade' => ['required', 'integer', 'min:0', 'gte:min_grade'],
            'description_prefix' => ['required', 'string', 'max:255'],
        ]);

        $gradeDescriptor->name = $validated['name'];
        $gradeDescriptor->code = $validated['code'];
        $gradeDescriptor->min_grade = $validated['min_grade'];
        $gradeDescriptor->max_grade = $validated['max_grade'];
        $gradeDescriptor->description_prefix = $validated['description_prefix'];

        $gradeDescriptor->save();
        return redirect()->route('grade-descriptors.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeDescriptor $gradeDescriptor)
    {
        GradeDescriptor::destroy($gradeDescriptor->id);
    }
}
