<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndicatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Indicator/Index', [
            'data' => fn () => Indicator::with('subindicators', 'units')
                ->orderBy('name', 'asc')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Indicator/Create', [
            'units' => fn () => Unit::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_ids' => ['required', 'array', 'min:1'],
            'unit_ids.*' => ['numeric', 'exists:units,id'],
        ]);

        $indicator = Indicator::create(['name' => $validated['name']]);
        $indicator->units()->attach($validated['unit_ids']);

        return redirect()->route('indicators.show', ['indicator' => $indicator]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Indicator $indicator)
    {
        return Inertia::render('Indicator/Show', [
            'data' => $indicator,
            'subindicators' => fn () => $indicator
                ->subindicators()
                ->orderBy('name', 'asc')
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Indicator $indicator)
    {
        return Inertia::render('Indicator/Edit', [
            'data' => $indicator->load('units'),
            'units' => fn () => Unit::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Indicator $indicator)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_ids' => ['required', 'array', 'min:1'],
            'unit_ids.*' => ['numeric', 'exists:units,id'],
        ]);

        $indicator->name = $validated['name'];
        $indicator->save();

        $indicator->units()->sync($validated['unit_ids']);

        return redirect()->route('indicators.show', ['indicator' => $indicator]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indicator $indicator)
    {
        Indicator::destroy($indicator->id);
    }
}