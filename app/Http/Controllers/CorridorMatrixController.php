<?php

namespace App\Http\Controllers;

use App\Models\Corridor;
use App\Models\CurrencyPair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CorridorMatrixController extends Controller
{
    public function index()
    {
        // Obtener todos los pares activos con sus divisas
        $pairs = CurrencyPair::with(['fromCurrency', 'toCurrency'])
            ->active()
            ->orderBy('from_currency_id')
            ->orderBy('to_currency_id')
            ->get();

        // Obtener todos los corredores activos
        $corridors = Corridor::active()
            ->orderBy('name')
            ->get();

        // Obtener todas las asignaciones actuales
        $assignments = DB::table('corridor_currency_pair')
            ->where('is_enabled', true)
            ->get()
            ->groupBy('currency_pair_id')
            ->map(function ($items) {
                return $items->pluck('corridor_id')->toArray();
            });

        return view('corridor-matrix.index', compact('pairs', 'corridors', 'assignments'));
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'pair_id' => 'required|exists:currency_pairs,id',
            'corridor_id' => 'required|exists:corridors,id',
        ]);

        $pair = CurrencyPair::findOrFail($validated['pair_id']);
        $corridorId = $validated['corridor_id'];

        // Verificar si existe la relación
        $exists = $pair->corridors()->where('corridor_id', $corridorId)->exists();

        if ($exists) {
            // Si existe, obtener estado actual y togglear
            $currentState = $pair->corridors()
                ->where('corridor_id', $corridorId)
                ->first()
                ->pivot
                ->is_enabled;

            $newState = !$currentState;

            $pair->corridors()->updateExistingPivot($corridorId, [
                'is_enabled' => $newState,
            ]);

            return response()->json([
                'success' => true,
                'enabled' => $newState,
                'message' => $newState ? 'Corredor habilitado' : 'Corredor deshabilitado',
            ]);
        } else {
            // Si no existe, crear con is_enabled = true
            $pair->corridors()->attach($corridorId, ['is_enabled' => true]);

            return response()->json([
                'success' => true,
                'enabled' => true,
                'message' => 'Corredor asignado y habilitado',
            ]);
        }
    }
}
