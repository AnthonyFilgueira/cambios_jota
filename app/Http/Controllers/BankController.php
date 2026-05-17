<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Country;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function store(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'swift_code' => 'nullable|string|max:20',
        ]);

        $validated['country_id'] = $country->id;
        Bank::create($validated);

        return redirect()->route('countries.show', $country)->with('success', 'Banco agregado correctamente.');
    }

    public function update(Request $request, Country $country, Bank $bank)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'swift_code' => 'nullable|string|max:20',
        ]);

        $bank->update($validated);

        return redirect()->route('countries.show', $country)->with('success', 'Banco actualizado.');
    }

    public function toggleActive(Country $country, Bank $bank)
    {
        $bank->update(['active' => !$bank->active]);

        return response()->json(['active' => $bank->active]);
    }
}
