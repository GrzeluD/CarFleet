<?php

namespace App\Http\Controllers;

use App\Models\CostType;
use App\Models\Vehicle;
use App\Models\VehicleCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VehicleCostController extends Controller
{
    public function index()
    {
        $vehicleCosts = VehicleCost::all();
        $vehicles = Vehicle::all();
        $costTypes = CostType::all();

        return Inertia::render('VehicleCosts/VehicleCosts', [
            'vehicleCosts' => $vehicleCosts,
            'vehicles' => $vehicles,
            'costTypes' => $costTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'date' => 'required|date',
            'cost_type_id' => 'required|exists:cost_types,cost_type_id',
            'description' => 'nullable|string|max:255',
            'amount_gross' => 'required|numeric|min:0',
            'vat_rate' => 'required|numeric',
            'amount_net' => 'required|numeric',
            'vat_amount' => 'required|numeric',
            'invoice_path' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        if ($request->hasFile('invoice_path')) {
            $file = $request->file('invoice_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/invoices', $filename);

            $validatedData['invoice_path'] = str_replace('public/', 'storage/', $path);
        }

        VehicleCost::create($validatedData);

        return response()->json([
            'message' => 'Koszt został dodany.',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        dd($request->all());

        $validatedData = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'cost_type_id' => 'required|exists:cost_types,cost_type_id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'amount_gross' => 'required|numeric',
            'vat_rate' => 'required|numeric',
            'amount_net' => 'required|numeric',
            'vat_amount' => 'required|numeric',
            'invoice_path' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $vehicleCost = VehicleCost::findOrFail($id);

        if ($request->hasFile('invoice_path')) {
            $file = $request->file('invoice_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/invoices', $filename);

            $validatedData['invoice_path'] = str_replace('public/', 'storage/', $path);

            if ($vehicleCost->invoice_path) {
                $oldFilePath = str_replace('storage/', 'public/', $vehicleCost->invoice_path);
                if (Storage::exists($oldFilePath)) {
                    Storage::delete($oldFilePath);
                }
            }
        } else {
            unset($validatedData['invoice_path']);
        }

        $vehicleCost->update($validatedData);

        return response()->json([
            'message' => 'Koszt został zaktualizowany.',
            'data' => $vehicleCost,
        ]);
    }



    public function destroy(string $id)
    {
        //
    }
}
