<?php

namespace App\Http\Controllers\Pengaturan;

use App\Models\Pengaturan\PpdbFormSetting;
use App\Models\Keuangan\BiayaMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class PengaturanFormPpdbController extends Controller
{
    public function index()
    {
        $muridFields   = PpdbFormSetting::where('field_category', 'murid')->orderBy('sort_order')->get();
        $ortuFields    = PpdbFormSetting::where('field_category', 'ortu')->orderBy('sort_order')->get();
        $waliFields    = PpdbFormSetting::where('field_category', 'wali')->orderBy('sort_order')->get();
        $dokumenFields = PpdbFormSetting::where('field_category', 'dokumen')->orderBy('sort_order')->get();
        $biayas        = BiayaMurid::with('account')->orderBy('id')->get();

        return view('admin.pengaturan.pengaturan_form_ppdb', compact(
            'muridFields', 'ortuFields', 'waliFields', 'dokumenFields', 'biayas'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings'             => 'required|array',
            'settings.*.is_active'   => 'boolean',
            'settings.*.is_required' => 'boolean',
        ]);

        foreach ($request->settings as $fieldName => $setting) {
            $formSetting = PpdbFormSetting::where('field_name', $fieldName)->first();
            if ($formSetting) {
                $formSetting->update([
                    'is_active'   => $setting['is_active']   ?? false,
                    'is_required' => $setting['is_required'] ?? false,
                ]);
            }
        }

        // Update biaya settings
        if ($request->has('biaya_settings')) {
            foreach ($request->biaya_settings as $biayaId => $setting) {
                $biaya = BiayaMurid::find($biayaId);
                if ($biaya) {
                    $biaya->update([
                        'is_active'       => $setting['is_active']       ?? true,
                        'disabled_reason' => $setting['disabled_reason'] ?? null,
                    ]);
                }
            }
        }

        // Hapus cache form settings & biaya agar perubahan langsung aktif di halaman PPDB
        Cache::forget('ppdb_form_settings');
        Cache::forget('ppdb_biayas');

        return back()->with('success', 'Pengaturan form PPDB berhasil disimpan');
    }
}
