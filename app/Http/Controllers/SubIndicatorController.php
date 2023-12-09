<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\SubIndicator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubIndicatorController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Indicator $indicator)
    {
        return Inertia::render('SubIndicator/Create', [
            'indicator' => $indicator,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Indicator $indicator)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $indicator->subindicators()->create($validated);
        return redirect()->route('indicators.show', ['indicator' => $indicator]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubIndicator $subIndicator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Indicator $indicator, SubIndicator $subindicator)
    {
        return Inertia::render('SubIndicator/Edit', [
            'indicator' => $indicator,
            'data' => $subindicator,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Indicator $indicator, SubIndicator $subindicator)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $subindicator->name = $validated['name'];
        $subindicator->save();

        return redirect()->route('indicators.show', ['indicator' => $indicator]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Indicator $indicator, SubIndicator $subindicator)
    {
        SubIndicator::destroy($subindicator->id);
    }
}
