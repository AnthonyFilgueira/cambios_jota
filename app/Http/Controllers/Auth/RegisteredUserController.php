<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone'       => ['required', 'string', 'max:30'],
            'vendor_code' => ['required', 'string', 'exists:sellers,code'],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'vendor_code.required' => 'El código de vendedor es obligatorio.',
            'vendor_code.exists'   => 'El código de vendedor no existe o no está registrado.',
            'phone.required'       => 'El teléfono es obligatorio.',
        ]);

        $seller = Seller::where('code', $request->vendor_code)->firstOrFail();

        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'phone'              => $request->phone,
            'assigned_seller_id' => $seller->id,
            'password'           => Hash::make($request->password),
        ]);

        $user->assignRole('cliente');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
