<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    protected string $settingsPath = 'settings.json';

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
            'enable_delete_modal' => 'nullable|boolean',
            'show_tax_in_totals' => 'nullable|boolean',
            'min_invoice_gram_avg' => 'required|numeric|min:0',
        ]);

        // Normalize booleans
        $validated['enable_delete_modal'] = (bool)($validated['enable_delete_modal'] ?? false);
        $validated['show_tax_in_totals'] = (bool)($validated['show_tax_in_totals'] ?? false);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            // Store a web-accessible path
            $validated['logo_path'] = 'storage/' . $path;
            unset($validated['logo']);
        }

        $current = $this->readSettings();
        $merged = array_merge($current, $validated);
        $this->writeSettings($merged);

        return redirect()->route('settings.index')->with('success', 'تم حفظ إعدادات النظام بنجاح');
    }

    private function readSettings(): array
    {
        if (Storage::exists($this->settingsPath)) {
            $json = Storage::get($this->settingsPath);
            return json_decode($json, true) ?: [];
        }
        return [
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
    }

    private function writeSettings(array $settings): void
    {
        Storage::put($this->settingsPath, json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
