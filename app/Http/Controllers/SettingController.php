<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $groups   = collect(Setting::DEFINITIONS)
            ->groupBy(fn ($def) => $def['group'])
            ->keys();

        return view('admin.settings', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            if (!array_key_exists($key, Setting::DEFINITIONS)) continue;

            $def  = Setting::DEFINITIONS[$key];
            $val  = $def['type'] === 'boolean' ? ($value === '1' ? '1' : '0') : $value;
            Setting::set($key, $val);
        }

        // Manejar checkboxes que no se envían cuando están desmarcados
        foreach (Setting::DEFINITIONS as $key => $def) {
            if ($def['type'] === 'boolean' && !isset($inputs[$key])) {
                Setting::set($key, '0');
            }
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Configuración guardada correctamente.');
    }
}
