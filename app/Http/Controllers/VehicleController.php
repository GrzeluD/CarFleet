<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\InspectionDue3DaysNotification;
use App\Notifications\InspectionDue7DaysNotification;
use App\Notifications\InspectionExpiredNotification;
use App\Notifications\InsuranceDue3DaysNotification;
use App\Notifications\InsuranceDue7DaysNotification;
use App\Notifications\InsuranceExpiredNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();
        return Inertia::render('Vehicles/Vehicles', ['vehicles' => $vehicles]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vin' => 'required|string|max:255',
            'production_year' => 'required|integer|min:1900|max:' . date('Y'),
            'license_plate' => 'required|string|max:255|unique:vehicles',
            'insurance_expiry' => 'required|date',
            'inspection_due' => 'required|date',
        ]);

        $vehicle = Vehicle::create($validatedData);

        $this->scheduleNotifications($vehicle);

        return response()->json([
            'message' => 'Pojazd został dodany.',
            'vehicle' => $vehicle,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validatedData = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'production_year' => 'required|integer|min:1900|max:' . date('Y'),
            'license_plate' => 'required|string|max:255|unique:vehicles,license_plate,' . $id . ',vehicle_id',
            'insurance_expiry' => 'required|date',
            'inspection_due' => 'required|date',
        ]);

        $vehicle->update($validatedData);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $notifications = $admin->notifications()->where('data->vehicle_id', $vehicle->vehicle_id);
            if ($notifications->exists()) {
                $notifications->delete();
            }
        }

        $this->scheduleNotifications($vehicle);

        return response()->json([
            'message' => 'Pojazd został zaktualizowany.',
            'vehicle' => $vehicle,
        ], 200);
    }

    public function destroy(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json([
            'message' => 'Pojazd został usunięty.',
        ], 200);
    }

    public function scheduleNotifications($vehicle)
    {
        $inspectionDate = Carbon::parse($vehicle->inspection_due);
        $insuranceDate = Carbon::parse($vehicle->insurance_expiry);

        $currentDate = now();

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $daysDifferenceInspection = $currentDate->diffInDays($inspectionDate, false);
            $daysDifferenceInsurance = $currentDate->diffInDays($insuranceDate, false);

            if ($daysDifferenceInspection == 7) {
                $admin->notify(new InspectionDue7DaysNotification($vehicle));
            }
            if ($daysDifferenceInspection == 3) {
                $admin->notify(new InspectionDue3DaysNotification($vehicle));
            }
            if ($inspectionDate->isToday()) {
                $admin->notify(new InspectionExpiredNotification($vehicle));
            }

            if ($daysDifferenceInsurance == 7) {
                $admin->notify(new InsuranceDue7DaysNotification($vehicle));
            }
            if ($daysDifferenceInsurance == 3) {
                $admin->notify(new InsuranceDue3DaysNotification($vehicle));
            }
            if ($insuranceDate->isToday()) {
                $admin->notify(new InsuranceExpiredNotification($vehicle));
            }
        }
    }
}
