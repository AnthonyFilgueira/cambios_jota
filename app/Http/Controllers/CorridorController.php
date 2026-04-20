<?php

namespace App\Http\Controllers;

use App\Models\Corridor;
use Illuminate\Http\Request;

class CorridorController extends Controller
{
    public function index()
    {
        $corridors = Corridor::orderBy('name')->get();

        return view('corridors.index', compact('corridors'));
    }

    public function create()
    {
        return view('corridors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:corridors,name',
            'description' => 'nullable|string|max:1000',
            'logo_url' => 'nullable|url|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Corridor::create($validated);

        return redirect()->route('corridors.index')
            ->with('success', 'Corredor creado exitosamente.');
    }

    public function edit(Corridor $corridor)
    {
        return view('corridors.edit', compact('corridor'));
    }

    public function update(Request $request, Corridor $corridor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:corridors,name,' . $corridor->id,
            'description' => 'nullable|string|max:1000',
            'logo_url' => 'nullable|url|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $corridor->update($validated);

        return redirect()->route('corridors.index')
            ->with('success', 'Corredor actualizado exitosamente.');
    }

    public function destroy(Corridor $corridor)
    {
        // No eliminar, solo desactivar
        $corridor->deactivate();

        return redirect()->route('corridors.index')
            ->with('success', 'Corredor desactivado exitosamente.');
    }

    public function toggleStatus(Corridor $corridor)
    {
        $corridor->toggleStatus();

        $status = $corridor->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Corredor {$status} exitosamente.");
    }
}
