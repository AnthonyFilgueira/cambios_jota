<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('model_type', 'like', "%{$search}%")
                  ->orWhere('model_id', $search)
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($action = $request->get('action')) {
            $query->where('action', $action);
        }

        if ($model = $request->get('model')) {
            $query->where('model_type', 'like', "%{$model}%");
        }

        if ($from = $request->get('from')) {
            $query->where('created_at', '>=', $from . ' 00:00:00');
        }

        if ($to = $request->get('to')) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        $logs = $query->paginate(50)->withQueryString();

        $modelTypes = AuditLog::select('model_type')->distinct()->pluck('model_type')
            ->map(fn ($t) => class_basename($t))->sort()->values();

        return view('admin.audit-logs', compact('logs', 'modelTypes'));
    }
}
