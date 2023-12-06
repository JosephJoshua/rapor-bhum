<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Teacher/Index', [
            'data' => fn () =>
                User::with('schoolClasses')
                    ->where('role', '=', 'teacher')
                    ->orderBy('name', 'asc')
                    ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Teacher/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'teacher',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('teachers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $teacher)
    {
        return Inertia::render('Teacher/Edit', [
            'data' => $teacher,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $teacher->id],
            'password' => ['sometimes', 'nullable', 'string', 'confirmed', 'min:8'],
        ]);

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];

        if ($validated['password'] !== null)
        {
            $teacher->password = Hash::make($validated['password']);
        }

        $teacher->save();
        return redirect()->route('teachers.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $teacher)
    {
        User::destroy($teacher->id);
    }
}
