<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CostTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('vehicles', VehicleController::class)->middleware('auth');
    Route::resource('users', AdminUserController::class);

    Route::get('/cost-types', [CostTypeController::class, 'index'])->name('cost-types.index');
    Route::get('/cost-types/create', [CostTypeController::class, 'create'])->name('cost-types.create');
    Route::post('/cost-types', [CostTypeController::class, 'store'])->name('cost-types.store');
    Route::get('/cost-types/{id}/edit', [CostTypeController::class, 'edit'])->name('cost-types.edit');
    Route::put('/cost-types/{id}', [CostTypeController::class, 'update'])->name('cost-types.update');
    Route::patch('/cost-types/{id}', [CostTypeController::class, 'update'])->name('cost-types.update');
    Route::delete('/cost-types/{id}', [CostTypeController::class, 'destroy'])->name('cost-types.destroy');
});

require __DIR__.'/auth.php';
