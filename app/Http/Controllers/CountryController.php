<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DocumentType;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $active   = Country::with(['activeBanks', 'activeBusinessAccounts'])->where('active', true)->get();
        $inactive = Country::with(['activeBanks', 'activeBusinessAccounts'])->where('active', false)->get();

        return view('countries.index', compact('active', 'inactive'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'code_iso'      => 'required|string|size:2|unique:countries,code_iso',
            'emoji'         => 'nullable|string|max:10',
            'currency_name' => 'nullable|string|max:100',
            'role'          => 'required|in:origin,destination,both',
        ]);

        $validated['code_iso'] = strtoupper($validated['code_iso']);
        Country::create($validated);

        return redirect()->route('countries.index')->with('success', 'País registrado correctamente.');
    }

    public function show(Country $country)
    {
        $country->load(['banks', 'businessAccounts.bank', 'businessAccounts.sellers']);
        $activeBanks      = $country->banks()->where('active', true)->get();
        $inactiveBanks    = $country->banks()->where('active', false)->get();
        $activeAccounts   = $country->businessAccounts()->where('active', true)->with('bank', 'sellers')->get();
        $inactiveAccounts = $country->businessAccounts()->where('active', false)->with('bank', 'sellers')->get();
        $documentTypes  = DocumentType::where('country_id', $country->id)->orderBy('code')->get();
        $paymentMethods = PaymentMethod::where('country_id', $country->id)->orderBy('name')->get();

        return view('countries.show', compact(
            'country', 'activeBanks', 'inactiveBanks',
            'activeAccounts', 'inactiveAccounts', 'documentTypes', 'paymentMethods'
        ));
    }

    public function storeDocumentType(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20',
            'prefix'      => 'nullable|string|max:10',
            'placeholder' => 'nullable|string|max:50',
        ]);

        $validated['country_id'] = $country->id;
        DocumentType::create($validated);

        return redirect()->route('countries.show', $country)
            ->with('success', 'Tipo de documento creado.');
    }

    public function updateDocumentType(Request $request, Country $country, DocumentType $documentType)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'prefix'      => 'nullable|string|max:10',
            'placeholder' => 'nullable|string|max:50',
        ]);

        $documentType->update($validated);

        return redirect()->route('countries.show', $country)
            ->with('success', 'Tipo de documento actualizado.');
    }

    public function toggleDocumentType(Country $country, DocumentType $documentType)
    {
        $documentType->update(['active' => !$documentType->active]);

        return response()->json(['active' => $documentType->active]);
    }

    public function destroyDocumentType(Country $country, DocumentType $documentType)
    {
        $documentType->delete();

        return redirect()->route('countries.show', $country)
            ->with('success', 'Tipo de documento eliminado.');
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'emoji'         => 'nullable|string|max:10',
            'currency_name' => 'nullable|string|max:100',
            'role'          => 'required|in:origin,destination,both',
        ]);

        $country->update($validated);

        return redirect()->route('countries.show', $country)->with('success', 'País actualizado.');
    }

    public function toggleActive(Country $country)
    {
        $country->update(['active' => !$country->active]);

        return response()->json(['active' => $country->active]);
    }

    // ─── PAYMENT METHODS ─────────────────────────────────────────────────────────

    public function storePaymentMethod(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50',
        ]);

        $validated['country_id'] = $country->id;
        PaymentMethod::create($validated);

        return redirect()->route('countries.show', $country)
            ->with('success', 'Método de pago creado.');
    }

    public function updatePaymentMethod(Request $request, Country $country, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $paymentMethod->update($validated);

        return redirect()->route('countries.show', $country)
            ->with('success', 'Método de pago actualizado.');
    }

    public function togglePaymentMethod(Country $country, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['active' => !$paymentMethod->active]);

        return response()->json(['active' => $paymentMethod->active]);
    }

    public function destroyPaymentMethod(Country $country, PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('countries.show', $country)
            ->with('success', 'Método de pago eliminado.');
    }
}
