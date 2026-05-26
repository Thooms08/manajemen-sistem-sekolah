<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPPDBController extends Controller
{
    public function index()
    {
        return view('dashboard_admin.notif_ppdb');
    }

    // Ambil data murid status pending
    public function getNotifications()
    {
        $notifications = DB::table('murid')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($notifications);
    }

    // Ambil jumlah notifikasi untuk badge
    public function getBadgeCount()
    {
        $count = DB::table('murid')->where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }

    // Ambil detail murid dan wali untuk modal
    public function getDetail($id)
    {
        $murid = DB::table('murid')->where('id', $id)->first();
        $wali = DB::table('wali_murid')->where('id_murid', $id)->first();

        return response()->json([
            'murid' => $murid,
            'wali' => $wali
        ]);
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