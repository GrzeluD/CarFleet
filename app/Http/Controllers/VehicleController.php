<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
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
}
