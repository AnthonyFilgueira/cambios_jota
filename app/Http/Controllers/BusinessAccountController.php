<?php

namespace App\Http\Controllers;

use App\Models\BusinessAccount;
use App\Models\Country;
use App\Models\Seller;
use Illuminate\Http\Request;

class BusinessAccountController extends Controller
{
    public function store(Request $request, Country $country)
    {
        $validated = $request->validate([
            'bank_id'         => 'required|exists:banks,id',
            'account_number'  => 'required|string|max:30',
            'account_type'    => 'required|in:ahorro,corriente,movil',
            'account_holder'  => 'required|string|max:150',
            'dni_ruc'         => 'nullable|string|max:20',
            'alias'           => 'nullable|string|max:100',
        ]);

        $validated['country_id'] = $country->id;
        BusinessAccount::create($validated);

        return redirect()->route('countries.show', $country)->with('success', 'Cuenta registrada correctamente.');
    }

    public function update(Request $request, Country $country, BusinessAccount $businessAccount)
    {
        $validated = $request->validate([
            'bank_id'         => 'required|exists:banks,id',
            'account_number'  => 'required|string|max:30',
            'account_type'    => 'required|in:ahorro,corriente,movil',
            'account_holder'  => 'required|string|max:150',
            'dni_ruc'         => 'nullable|string|max:20',
            'alias'           => 'nullable|string|max:100',
        ]);

        $businessAccount->update($validated);

        return redirect()->route('countries.show', $country)->with('success', 'Cuenta actualizada.');
    }

    public function toggleActive(Country $country, BusinessAccount $businessAccount)
    {
        $businessAccount->update(['active' => !$businessAccount->active]);

        return response()->json(['active' => $businessAccount->active]);
    }

    public function assign(Request $request, BusinessAccount $businessAccount)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
        ]);

        $seller = Seller::findOrFail($request->seller_id);

        $existing = $businessAccount->allSellers()
            ->where('seller_id', $seller->id)
            ->first();

        if ($existing) {
            $businessAccount->allSellers()->updateExistingPivot($seller->id, ['unassigned_at' => null]);
        } else {
            $businessAccount->sellers()->attach($seller->id);
        }

        return response()->json(['success' => true, 'message' => 'Cuenta asignada al vendedor.']);
    }

    public function unassign(BusinessAccount $businessAccount, Seller $seller)
    {
        $businessAccount->sellers()->updateExistingPivot($seller->id, [
            'unassigned_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Cuenta desasignada del vendedor.']);
    }
}
