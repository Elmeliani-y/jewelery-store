<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = $this->readSettings();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'currency_symbol' => 'required|string|max:10',
            'address' => 'nullable|string|max:255',
            'phones' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'commercial_register' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'min_invoice_gram_avg' => 'required|numeric|min:0',
            'enable_delete_modal' => 'nullable',
            'show_tax_in_totals' => 'nullable',
            'logo_path' => 'nullable|string',
        ]);

        // Normalize booleans from checkboxes
        $validated['enable_delete_modal'] = $request->has('enable_delete_modal');
        $validated['show_tax_in_totals'] = $request->has('show_tax_in_totals');
        
        // Ensure min_invoice_gram_avg is saved as float
        $validated['min_invoice_gram_avg'] = (float)$validated['min_invoice_gram_avg'];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo_path'] = 'storage/' . $path;
        } else {
            // If no new logo uploaded, keep the old one from DB (not from request)
            $validated['logo_path'] = Setting::get('logo_path');
        }
        unset($validated['logo']);

        // Save each setting to database
        foreach ($validated as $key => $value) {
            Setting::set($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        // Always reload settings from DB after save
        $settings = $this->readSettings();
        return redirect()->route('h4i8j3k7')->with(['success' => 'تم حفظ إعدادات النظام بنجاح', 'settings' => $settings]);
    }

    private function readSettings(): array
    {
        $defaults = [
            'company_name' => config('app.name', 'متجر المجوهرات'),
            'currency_symbol' => 'ريال',
            'address' => '',
            'phones' => '',
            'tax_number' => '',
            'commercial_register' => '',
            'logo_path' => null,
            'enable_delete_modal' => true,
            'show_tax_in_totals' => true,
            'min_invoice_gram_avg' => config('sales.min_invoice_gram_avg', 2.0),
        ];

        $settings = [];
        foreach ($defaults as $key => $default) {
            $value = Setting::get($key, $default);
            // Convert string booleans back to actual booleans
            if (in_array($key, ['enable_delete_modal', 'show_tax_in_totals'])) {
                $settings[$key] = $value === '1' || $value === true;
            } else {
                $settings[$key] = $value;
            }
        }

        return $settings;
    }
}
