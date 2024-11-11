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

        CostType::create($validatedData);

        return redirect()->route('cost-types.index')->with('success', 'Typ kosztu został dodany.');
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'cost_type_name' => 'required|string|max:255',
        ]);

        $costType = CostType::findOrFail($id);
        $costType->update($validatedData);

        return redirect()->route('cost-types.index')->with('success', 'Typ kosztu został zaktualizowany.');
    }

    public function destroy(string $id)
    {
        $costType = CostType::findOrFail($id);
        $costType->delete();

        return redirect()->route('cost-types.index')->with('success', 'Typ kosztu został usunięty.');
    }
}
