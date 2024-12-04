<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CostTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleCostController;
use App\Http\Controllers\VehicleMileageController;
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
    return redirect()->route('vehicles.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('vehicles', VehicleController::class);
    Route::resource('users', AdminUserController::class);
    Route::resource('cost-types', CostTypeController::class);
    Route::resource('vehicle-costs', VehicleCostController::class);
    Route::resource('vehicle-mileages', VehicleMileageController::class)->except('show');
    Route::get('/vehicle-mileages/csv', [VehicleMileageController::class, 'generateCSV'])->name('vehicle-mileages.csv');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
});

require __DIR__.'/auth.php';
