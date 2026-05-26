<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Murid;

class PelanggaranController extends Controller
{
    public function index()
    {
        // Ambil data pelanggaran murid dengan join
        $pelanggaranMurid = DB::table('pelanggaran_murid')
            ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
            ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
            ->select('pelanggaran_murid.*', 'murid.nama_lengkap', 'murid.nisn', 'aturan_pelanggaran.nama_pelanggaran', 'aturan_pelanggaran.skor')
            ->orderBy('pelanggaran_murid.created_at', 'desc')
            ->get();

        // Data akumulasi skor per murid
        $akumulasiSkor = DB::table('pelanggaran_murid')
            ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
            ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
            ->select('murid.nama_lengkap', 'murid.nisn', DB::raw('SUM(aturan_pelanggaran.skor) as total_skor'))
            ->groupBy('murid.id', 'murid.nama_lengkap', 'murid.nisn')
            ->get();

        $murids = DB::table('murid')->get();
        $aturans = DB::table('aturan_pelanggaran')->get();

        return view('dashboard_admin.pelanggaran', compact('pelanggaranMurid', 'murids', 'aturans', 'akumulasiSkor'));
    }

    public function storeAturan(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'skor' => 'required|integer|min:1'
        ]);

        DB::table('aturan_pelanggaran')->insert([
            'nama_pelanggaran' => $request->nama_pelanggaran,
            'skor' => $request->skor,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Aturan pelanggaran berhasil ditambahkan.');
    }

    // Update pada method storePelanggaranMurid
public function storePelanggaranMurid(Request $request)
{
    $request->validate([
        'id_murid' => 'required|exists:murid,id',
        'id_aturan_pelanggaran' => 'required|exists:aturan_pelanggaran,id',
        'keterangan' => 'nullable|string'
    ]);

    DB::table('pelanggaran_murid')->insert([
        'id_murid' => $request->id_murid,
        'id_aturan_pelanggaran' => $request->id_aturan_pelanggaran,
        'keterangan' => $request->keterangan,
        'status' => 'pending', // Nilai default saat input
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return redirect()->back()->with('success', 'Catatan pelanggaran murid berhasil disimpan dan menunggu konfirmasi.');
}
    public function destroy($id)
    {
        DB::table('pelanggaran_murid')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }

    // Tambahkan method ini di dalam PelanggaranController
public function updateAturan(Request $request, $id)
{
    $request->validate([
        'nama_pelanggaran' => 'required|string|max:255',
        'skor' => 'required|integer|min:1'
    ]);

    DB::table('aturan_pelanggaran')->where('id', $id)->update([
        'nama_pelanggaran' => $request->nama_pelanggaran,
        'skor' => $request->skor,
        'updated_at' => now()
    ]);

    return redirect()->back()->with('success', 'Aturan berhasil diperbarui.');
}

public function destroyAturan($id)
{
    // Cek apakah aturan sedang digunakan di tabel pelanggaran_murid
    $isUsed = DB::table('pelanggaran_murid')->where('id_aturan_pelanggaran', $id)->exists();
    
    if($isUsed) {
        return redirect()->back()->with('error', 'Aturan tidak bisa dihapus karena sudah tercatat pada data murid.');
    }

    DB::table('aturan_pelanggaran')->where('id', $id)->delete();
    return redirect()->back()->with('success', 'Aturan berhasil dihapus.');
}

public function ajaxSearch(Request $request)
{
    $query = $request->get('search');

    $pelanggaranMurid = DB::table('pelanggaran_murid')
        ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
        ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
        ->select('pelanggaran_murid.*', 'murid.nama_lengkap', 'murid.nisn', 'aturan_pelanggaran.nama_pelanggaran', 'aturan_pelanggaran.skor')
        ->where('murid.nama_lengkap', 'LIKE', '%' . $query . '%')
        ->orWhere('murid.nisn', 'LIKE', '%' . $query . '%')
        ->orWhere('aturan_pelanggaran.nama_pelanggaran', 'LIKE', '%' . $query . '%')
        ->orderBy('pelanggaran_murid.created_at', 'desc')
        ->get();

    $output = "";
    if ($pelanggaranMurid->count() > 0) {
        foreach ($pelanggaranMurid as $p) {
            $rowColor = '';
            if($p->skor >= 50) $rowColor = 'skor-tinggi';
            elseif($p->skor >= 25) $rowColor = 'skor-mendekati';
            elseif($p->skor >= 10) $rowColor = 'skor-sedang';

            $output .= '
            <tr class="' . $rowColor . '">
                <td class="fw-bold">' . $p->nisn . '</td>
                <td>' . $p->nama_lengkap . '</td>
                <td><span class="badge bg-dark">' . $p->nama_pelanggaran . '</span></td>
                <td><small class="text-muted">' . ($p->keterangan ?? "-") . '</small></td>
                <td class="text-center fw-bold">' . $p->skor . '</td>
                <td class="text-center">
                    <form action="' . route('pelanggaran.destroy', $p->id) . '" method="POST">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button class="btn btn-sm text-danger" onclick="return confirm(\'Hapus catatan ini?\')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>';
        }
    } else {
        $output = '<tr><td colspan="6" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
    }

    return response($output);
}
}