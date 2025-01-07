<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMileage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehicleMileageController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleMileage::query();

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('driver_ids')) {
            $query->whereIn('driver_id', $request->driver_ids);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $vehicleMileages = $query->get();

        if ($request->wantsJson()) {
            return response()->json([
                'vehicleMileages' => $vehicleMileages,
            ]);
        }

        $vehicles = Vehicle::all();
        $drivers = User::all();

        return Inertia::render('VehicleMileages/VehicleMileages', [
            'vehicleMileages' => $vehicleMileages,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_mileage' => 'required|numeric|min:0',
            'end_mileage' => 'required|numeric|min:0|gte:start_mileage',
            'location_start' => 'required|string|max:255',
            'location_end' => 'required|string|max:255',
            'route_description' => 'nullable|string',
            'distance_traveled' => 'required|numeric|min:0',
        ]);

        VehicleMileage::create($validatedData);

        return response()->json(['message' => 'Przebieg został dodany.'], 201);
    }

    public function update(Request $request, $id)
    {
        $mileageRecord = VehicleMileage::findOrFail($id);

        $validatedData = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_mileage' => 'required|numeric|min:0',
            'end_mileage' => 'required|numeric|min:0|gte:start_mileage',
            'location_start' => 'required|string|max:255',
            'location_end' => 'required|string|max:255',
            'route_description' => 'nullable|string',
            'distance_traveled' => 'required|numeric|min:0',
        ]);

        $mileageRecord->update($validatedData);

        return response()->json(['message' => 'Przebieg został zaktualizowany.']);
    }

    public function generateCSV(Request $request)
    {
        $query = VehicleMileage::with(['vehicle', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $mileages = $query->get();

        $csvHeader = ['ID', 'Pojazd', 'Kierowca' , 'Data', 'Początkowy przebieg', 'Końcowy przebieg', 'Opis trasy', 'Dystans (km)'];
        $csvData = $mileages->map(function ($mileage) {
            return [
                $mileage->mileage_id,
                $mileage->vehicle->brand . ' - ' . $mileage->vehicle->license_plate,
                $mileage->user->name,
                date('Y-m-d', strtotime($mileage->date)),
                $mileage->start_mileage,
                $mileage->end_mileage,
                $mileage->route_description,
                $mileage->distance_traveled,
            ];
        });

        $filename = 'vehicle_mileages_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeader);

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }


    public function destroy($id)
    {
        $mileageRecord = VehicleMileage::findOrFail($id);
        $mileageRecord->delete();

        return response()->json(['message' => 'Przebieg został usunięty.']);
    }
}
