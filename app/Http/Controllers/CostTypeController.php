<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CostType;
use Inertia\Inertia;

class CostTypeController extends Controller
{
    public function index()
    {
        $costTypes = CostType::all();
        return Inertia::render('CostTypes/CostTypes', ['costTypes' => $costTypes]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cost_type_name' => 'required|string|max:255',
        ]);

        $costType = CostType::create($validatedData);

        return response()->json([
            'message' => 'Typ kosztu został dodany.',
            'costType' => $costType,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'cost_type_name' => 'required|string|max:255',
        ]);

        $costType = CostType::findOrFail($id);
        $costType->update($validatedData);

        return response()->json([
            'message' => 'Typ kosztu został zaktualizowany.',
            'costType' => $costType,
        ], 200);
    }

    public function destroy(string $id)
    {
        $costType = CostType::findOrFail($id);
        $costType->delete();

        return response()->json([
            'message' => 'Typ kosztu został usunięty.',
        ], 200);
    }
}
