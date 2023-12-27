<?php

use App\Http\Controllers\AcademicTermController;
use App\Http\Controllers\GradeDescriptorController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentSubIndicatorController;
use App\Http\Controllers\SubIndicatorController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UnitController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/school-classes', [SchoolClassController::class, 'index'])->name('school-classes.index');
    Route::put('/units/{unit}/school-classes/{school_class}/teacher', [SchoolClassController::class, 'updateTeacher'])->name('units.school-classes.update-teacher')->middleware('can:manage-classes');

    Route::resource('teachers', TeacherController::class)->except(['show'])->middleware('can:manage-teachers');
    Route::resource('units', UnitController::class)->middleware('can:manage-units');
    Route::resource('grade-descriptors', GradeDescriptorController::class)->except(['show'])->middleware('can:manage-grade-descriptors');

    Route::resource('indicators', IndicatorController::class)->middleware('can:manage-indicators');
    Route::resource('indicators.subindicators', SubIndicatorController::class)->except(['index'])->middleware('can:manage-indicators');

    Route::post('/academic-terms/{academic_term}/students/{student}/subindicators/{subindicator}', [StudentSubIndicatorController::class, 'store'])->name('academic-terms.students.subindicators.store');
    Route::delete('/academic-terms/{academic_term}/students/{student}/subindicators/{subindicator}', [StudentSubIndicatorController::class, 'destroy'])->name('academic-terms.students.subindicators.destroy');

    Route::resource('units.school-classes', SchoolClassController::class)->except(['index', 'show'])->middleware('can:manage-classes');
    Route::resource('units.school-classes', SchoolClassController::class)->only(['show']);
    Route::resource('units.school-classes.students', StudentController::class)->except(['index', 'show']);

    Route::resource('academic-terms', AcademicTermController::class)->except(['show'])->middleware('can:manage-academic-terms');

    Route::get('/report-card', [ReportCardController::class, 'show'])->name('report-card');
});

require __DIR__.'/auth.php';
