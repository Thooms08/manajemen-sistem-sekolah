<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\PpdbFormSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminPPDBController extends Controller
{
    public function index()
    {
        return view('dashboard_admin.notif_ppdb');
    }

    // Ambil data murid status pending + flag apakah ada biaya cash aktif
    public function getNotifications()
    {
        $notifications = DB::table('murid')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        // Cek apakah ada biaya cash aktif (tidak punya akun bank/QRIS)
        $hasCashBiaya = DB::table('biaya_murid')
            ->whereNull('account_id')
            ->where('is_active', true)
            ->exists();

        return response()->json([
            'murid'        => $notifications,
            'hasCashBiaya' => $hasCashBiaya,
        ]);
    }

    // Ambil daftar biaya cash aktif untuk modal konfirmasi
    public function getCashBiayas()
    {
        $biayas = DB::table('biaya_murid')
            ->whereNull('account_id')
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'amount']);

        return response()->json($biayas);
    }

    // Ambil jumlah notifikasi untuk badge
    public function getBadgeCount()
    {
        $count = DB::table('murid')->where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }

    // Ambil detail lengkap murid, ortu, wali, dokumen + form settings aktif
    public function getDetail($id)
    {
        $murid = Murid::with(['ortu', 'wali', 'dokumen'])->findOrFail($id);

        // Ambil semua field yang aktif, dikelompokkan per kategori
        $formSettings = PpdbFormSetting::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('field_category');

        // Siapkan data ortu & wali sebagai array (null-safe)
        $ortu     = $murid->ortu   ? $murid->ortu->toArray()   : [];
        $wali     = $murid->wali   ? $murid->wali->toArray()   : [];
        $dokumen  = $murid->dokumen ? $murid->dokumen->toArray() : [];

        // Buat URL publik untuk setiap file dokumen yang ada
        $dokumenUrls = [];
        if ($murid->dokumen) {
            $fileFields = [
                'pasfoto',
                'ktp_ayah', 'ktp_ibu', 'ktp_wali', 'kartu_keluarga',
                'akte_kelahiran', 'ijazah_terakhir', 'transkip_nilai',
                'surat_kelulusan', 'surat_keterangan_hasil_ujian',
                'surat_pindahan', 'formulir_fisik',
            ];
            foreach ($fileFields as $field) {
                if (!empty($dokumen[$field])) {
                    // Generate URL dengan signed route agar file privat bisa diakses sementara
                    $dokumenUrls[$field] = route('admin.ppdb.dokumen', [
                        'path' => base64_encode($dokumen[$field])
                    ]);
                }
            }
        }

        // Kelompokkan form settings per kategori menjadi array sederhana
        $settings = [];
        foreach ($formSettings as $category => $fields) {
            foreach ($fields as $field) {
                $settings[$category][] = [
                    'field_name'  => $field->field_name,
                    'field_label' => $field->field_label,
                ];
            }
        }

        return response()->json([
            'murid'        => $murid->toArray(),
            'ortu'         => $ortu,
            'wali'         => $wali,
            'dokumen_urls' => $dokumenUrls,
            'settings'     => $settings,
        ]);
    }

    // Serve file dokumen privat untuk admin
    public function serveDokumen(Request $request)
    {
        $path = base64_decode($request->query('path'));

        if (!$path || !Storage::exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::response($path);
    }

    // Konfirmasi Pendaftaran
    public function confirm($id)
    {
        $update = DB::table('murid')
            ->where('id', $id)
            ->update([
                'status' => 'konfirmasi',
                'updated_at' => now()
            ]);

        if ($update) {
            return response()->json(['success' => true, 'message' => 'Pendaftaran berhasil dikonfirmasi']);
        }

        return response()->json(['success' => false], 500);
    }


public function getStatus() {
    $status = DB::table('profile_sekolah')->value('is_ppdb_open');
    return response()->json(['isOpen' => (bool)$status]);
}

public function toggleStatus() {
    $currentStatus = DB::table('profile_sekolah')->value('is_ppdb_open');
    $newStatus = !$currentStatus;

    DB::table('profile_sekolah')->update(['is_ppdb_open' => $newStatus]);

    return response()->json([
        'success' => true, 
        'isOpen' => $newStatus,
        'message' => $newStatus ? 'Pendaftaran PPDB Berhasil Dibuka' : 'Pendaftaran PPDB Berhasil Ditutup'
    ]);
}
}