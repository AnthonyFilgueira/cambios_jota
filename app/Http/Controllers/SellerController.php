<?php

namespace App\Http\Controllers;

use App\Models\BusinessAccount;
use App\Models\CommissionRule;
use App\Models\Country;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::with(['user', 'businessAccounts.bank', 'businessAccounts.country'])
            ->addSelect([
                'clients_count' => User::selectRaw('count(*)')
                    ->whereColumn('assigned_seller_id', 'sellers.id'),
            ])
            ->get();

        return view('sellers.index', compact('sellers'));
    }

    public function create()
    {
        [$businessAccounts, $countries] = $this->accountsByCountry();
        return view('sellers.create', compact('businessAccounts', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'seller_commission' => 'required|numeric|min:0|max:100',
            'boss_commission'   => 'required|numeric|min:0|max:100',
        ]);

        // Crear usuario y asignar rol vendedor
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('vendedor');

        // Crear vendedor vinculado al usuario
        $seller = Seller::create([
            'user_id'           => $user->id,
            'name'              => $request->name,
            'seller_commission' => $request->seller_commission,
            'boss_commission'   => $request->boss_commission,
        ]);

        $this->syncSellerAccounts($seller, $request->input('business_accounts', []));

        return redirect()->route('sellers.index')->with('success', 'Vendedor creado correctamente.');
    }

    public function edit(Seller $seller)
    {
        $seller->load('user');
        [$businessAccounts, $countries] = $this->accountsByCountry();
        $assignedAccountIds = $seller->businessAccounts->pluck('id')->toArray();

        return view('sellers.edit', compact('seller', 'businessAccounts', 'countries', 'assignedAccountIds'));
    }

    public function update(Request $request, Seller $seller)
    {
        $userId = $seller->user_id;

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => "required|email|unique:users,email,{$userId}",
            'password'          => 'nullable|string|min:8|confirmed',
            'seller_commission' => 'required|numeric|min:0|max:100',
            'boss_commission'   => 'required|numeric|min:0|max:100',
        ]);

        $changingCommissions = (
            $request->seller_commission != $seller->seller_commission ||
            $request->boss_commission   != $seller->boss_commission
        );

        if ($changingCommissions && !$seller->commissionsCanBeModified()) {
            return redirect()->route('sellers.index')->with('error',
                'No se pueden modificar las comisiones de este vendedor porque ya tiene ventas registradas.');
        }

        $seller->update($request->only('name', 'seller_commission', 'boss_commission'));

        // Actualizar usuario vinculado
        if ($seller->user) {
            $userData = ['name' => $request->name, 'email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $seller->user->update($userData);
        }

        $this->syncSellerAccounts($seller, $request->input('business_accounts', []));

        return redirect()->route('sellers.index')->with('success', 'Vendedor actualizado correctamente.');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();
        return redirect()->route('sellers.index')->with('success', 'Vendedor eliminado.');
    }

    public function commissions(Seller $seller)
    {
        $rules  = $seller->commissionRules()->with('appliedBy')->get();
        $latest = $rules->first();
        return view('sellers.commissions', compact('seller', 'rules', 'latest'));
    }

    public function storeCommission(Request $request, Seller $seller)
    {
        $validated = $request->validate([
            'commission_type' => 'required|in:percentage,fixed',
            'seller_value'    => 'required|numeric|min:0|max:100',
            'boss_value'      => 'required|numeric|min:0|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        $validated['seller_id']  = $seller->id;
        $validated['applied_by'] = auth()->id();

        CommissionRule::create($validated);

        $seller->update([
            'seller_commission' => $validated['seller_value'],
            'boss_commission'   => $validated['boss_value'],
        ]);

        return redirect()->route('sellers.commissions', $seller)
            ->with('success', 'Regla de comisión guardada y aplicada correctamente.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function accountsByCountry(): array
    {
        $accounts   = BusinessAccount::with('bank', 'country')->where('active', true)->get();
        $grouped    = $accounts->groupBy('country_id');
        $countryIds = $grouped->keys()->filter()->all();
        $countries  = Country::whereIn('id', $countryIds)->get()->keyBy('id');

        return [$grouped, $countries];
    }

    private function syncSellerAccounts(Seller $seller, array $selectedIds): void
    {
        DB::table('business_account_seller')
            ->where('seller_id', $seller->id)
            ->whereNull('unassigned_at')
            ->update(['unassigned_at' => now()]);

        foreach ($selectedIds as $accountId) {
            DB::table('business_account_seller')->updateOrInsert(
                ['seller_id' => $seller->id, 'business_account_id' => $accountId],
                ['unassigned_at' => null, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
