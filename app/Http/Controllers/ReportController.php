<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date') ?? now()->startOfWeek()->toDateString();
        $end = $request->input('end_date') ?? now()->endOfWeek()->toDateString();

        $sellers = Seller::with(['sales' => function ($query) use ($start, $end) {
            $query->whereBetween('sale_date', [$start, $end])->orderBy('sale_date');
        }])->get();

        return view('reports.index', compact('sellers', 'start', 'end'));
    }

}