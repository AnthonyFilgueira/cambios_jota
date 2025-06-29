<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::all();
        return view('sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'seller_commission' => 'required|numeric',
            'boss_commission' => 'required|numeric',
        ]);

        Seller::create($request->except('_token'));
        return redirect()->route('sellers.index');
    }

    public function edit(Seller $seller)
    {
        return view('sellers.edit', compact('seller'));
    }

    public function update(Request $request, Seller $seller)
    {
        $seller->update($request->except('_token'));
        return redirect()->route('sellers.index');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();
        return redirect()->route('sellers.index');
    }
}