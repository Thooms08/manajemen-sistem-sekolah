<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::all();
        return view('dashboard_admin.guru', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'email' => 'required|email|unique:guru,email',
            'no_whatsapp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        Guru::create($request->all());

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_guru' => 'required|string|max:255',
            'email' => 'required|email|unique:guru,email,' . $id,
            'no_whatsapp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        $guru = Guru::findOrFail($id);
        $guru->update($request->all());

        return redirect()->back()->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Guru::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data guru berhasil dihapus!');
    }
    public function search(Request $request)
{
    $query = $request->get('search');
    $output = "";

    // Mencari berdasarkan nama, email, atau no whatsapp
    $gurus = Guru::where('nama_guru', 'LIKE', '%' . $query . '%')
        ->orWhere('email', 'LIKE', '%' . $query . '%')
        ->orWhere('no_whatsapp', 'LIKE', '%' . $query . '%')
        ->get();

    if ($gurus->count() > 0) {
        foreach ($gurus as $index => $g) {
            $output .= '
            <tr>
                <td>' . ($index + 1) . '</td>
                <td class="fw-bold">' . $g->nama_guru . '</td>
                <td>' . $g->email . '</td>
                <td>' . $g->no_whatsapp . '</td>
                <td>' . \Illuminate\Support\Str::limit($g->alamat, 40) . '</td>
                <td class="text-center">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary border-0" 
                            onclick="openEditModal(\'' . $g->id . '\', \'' . $g->nama_guru . '\', \'' . $g->email . '\', \'' . $g->no_whatsapp . '\', \'' . $g->alamat . '\')">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="' . route('guru.destroy', $g->id) . '" method="POST" onsubmit="return confirm(\'Hapus data guru ini?\')">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>';
        }
    } else {
        $output = '<tr><td colspan="6" class="text-center py-4 text-muted">Data guru tidak ditemukan</td></tr>';
    }

    return response($output);
}

public function pelanggaran()
{
    $kelas = DB::table('kelas')->get();
    $aturans = DB::table('aturan_pelanggaran')->get();

    // Mengambil riwayat pelanggaran dengan status
    $riwayatPelanggaran = DB::table('pelanggaran_murid')
        ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
        ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
        ->leftJoin('murid_kelas', 'murid.id', '=', 'murid_kelas.id_murid')
        ->leftJoin('kelas', 'murid_kelas.id_kelas', '=', 'kelas.id')
        ->select(
            'pelanggaran_murid.*', 
            'murid.nama_lengkap', 
            'murid.nisn', 
            'aturan_pelanggaran.nama_pelanggaran', 
            'aturan_pelanggaran.skor',
            'kelas.nama_kelas'
        )
        ->orderBy('pelanggaran_murid.created_at', 'desc')
        ->get();

    return view('dashboard_guru.pelanggaran', compact('kelas', 'aturans', 'riwayatPelanggaran'));
}

public function searchPelanggaran(Request $request)
{
    $query = $request->get('search');

    $riwayatPelanggaran = DB::table('pelanggaran_murid')
        ->join('murid', 'pelanggaran_murid.id_murid', '=', 'murid.id')
        ->join('aturan_pelanggaran', 'pelanggaran_murid.id_aturan_pelanggaran', '=', 'aturan_pelanggaran.id')
        ->leftJoin('murid_kelas', 'murid.id', '=', 'murid_kelas.id_murid')
        ->leftJoin('kelas', 'murid_kelas.id_kelas', '=', 'kelas.id')
        ->select('pelanggaran_murid.*', 'murid.nama_lengkap', 'murid.nisn', 'aturan_pelanggaran.nama_pelanggaran', 'aturan_pelanggaran.skor', 'kelas.nama_kelas')
        ->where(function($q) use ($query) {
            $q->where('murid.nama_lengkap', 'LIKE', '%' . $query . '%')
              ->orWhere('murid.nisn', 'LIKE', '%' . $query . '%')
              ->orWhere('aturan_pelanggaran.nama_pelanggaran', 'LIKE', '%' . $query . '%');
        })
        ->orderBy('pelanggaran_murid.created_at', 'desc')
        ->get();

    $output = "";
    if ($riwayatPelanggaran->count() > 0) {
        foreach ($riwayatPelanggaran as $rp) {
            // Tentukan Badge Status
            $statusBadge = '';
            if($rp->status == 'pending') {
                $statusBadge = '<span class="badge bg-warning text-dark px-3 py-2">Pending</span>';
            } elseif($rp->status == 'konfirmasi') {
                $statusBadge = '<span class="badge bg-success px-3 py-2">Dikonfirmasi</span>';
            } else {
                $statusBadge = '<span class="badge bg-danger px-3 py-2">Ditolak</span>';
            }

            $output .= '
            <tr>
                <td>
                    <div class="fw-bold text-dark">' . $rp->nama_lengkap . '</div>
                    <small class="text-muted">NISN: ' . $rp->nisn . '</small>
                </td>
                <td>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary mb-1">' . ($rp->nama_kelas ?? "Tanpa Kelas") . '</span>
                    <div>' . $rp->nama_pelanggaran . '</div>
                </td>
                <td class="text-center"><span class="text-danger fw-bold">+' . $rp->skor . '</span></td>
                <td class="text-center">' . $statusBadge . '</td>
                <td>
                    <div class="small">' . date('d M Y', strtotime($rp->created_at)) . '</div>
                </td>
            </tr>';
        }
    } else {
        $output = '<tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan</td></tr>';
    }

    return response($output);
}
}