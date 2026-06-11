<?php

namespace App\Http\Controllers;

use App\Models\DataMaster\Murid;
use App\Models\Dokumen\Kelulusan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AlumniController extends Controller
{
    /**
     * Tampilan Utama Halaman Alumni
     */
    public function index()
    {
        // Ambil daftar tahun lulus unik dari tabel kelulusan (dokumen_db)
        $years = Kelulusan::whereNotNull('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        // Cukup query langsung dari status='lulus' di tabel murid — data mutlak
        $alumnis = Murid::where('status', 'lulus')
            ->with(['kelas', 'kelulusan'])
            ->get();

        return view('admin.data_master.alumni', compact('alumnis', 'years'));
    }

    /**
     * Fitur Pencarian & Filter Dropdown AJAX Real-time
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $tahun  = $request->get('tahun');

        // Query langsung ke murid dengan status lulus — data mutlak
        $muridQuery = Murid::where('status', 'lulus')
            ->with(['kelas', 'kelulusan']);

        // Filter per tahun lulus — perlu cross-DB karena tahun_lulus ada di dokumen_db
        if (!empty($tahun)) {
            $idsByTahun = Kelulusan::where('tahun_lulus', $tahun)->pluck('id_murid');
            $muridQuery->whereIn('id', $idsByTahun);
        }

        if (!empty($search)) {
            $muridQuery->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                  ->orWhere('nisn', 'LIKE', "%{$search}%")
                  ->orWhere('nis_baru', 'LIKE', "%{$search}%");
            });
        }

        $alumnis = $muridQuery->get();

        $html = '';
        if ($alumnis->isEmpty()) {
            $html .= '<tr><td colspan="7" class="text-center text-muted py-3">Data alumni tidak ditemukan</td></tr>';
        } else {
            foreach ($alumnis as $alumni) {
                // 1. Definisikan UUID terlebih dahulu agar tidak terjadi error path kosong
                $kelulusanUuid = $alumni->kelulusan->uuid ?? '';

                // 2. Generate URL Berkas mengikuti skema penamaan route privat dari KelulusanController secara eksak
                $suratUrl  = (!empty($alumni->kelulusan->surat_kelulusan) && $kelulusanUuid)
                    ? route('kelulusan.view.surat', $kelulusanUuid) : '';
                $ijazahUrl = (!empty($alumni->kelulusan->ijazah) && $kelulusanUuid)
                    ? route('kelulusan.view.ijazah', $kelulusanUuid) : '';
                $raportUrl = (!empty($alumni->kelulusan->raport) && $kelulusanUuid)
                    ? route('kelulusan.view.raport', $kelulusanUuid) : '';

                // 3. Ambil komponen tombol icon berkas
                $suratBtn = $suratUrl
                    ? '<a href="'.$suratUrl.'" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-check-fill"></i></a>'
                    : '-';
                $ijazahBtn = $ijazahUrl
                    ? '<a href="'.$ijazahUrl.'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-pdf"></i></a>'
                    : '-';
                $raportBtn = $raportUrl
                    ? '<a href="'.$raportUrl.'" target="_blank" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-text"></i></a>'
                    : '-';

                $nisBaru    = $alumni->nis_baru ?? '-';
                $tahunLulus = $alumni->kelulusan->tahun_lulus ?? '-';

                $html .= '<tr>
                    <td>' . e($alumni->nisn) . '</td>
                    <td>' . e($nisBaru) . '</td>
                    <td>' . e($alumni->nama_lengkap) . '</td>
                    <td class="text-center">' . e($tahunLulus) . '</td>
                    <td class="text-center">' . $suratBtn . '</td>
                    <td class="text-center">' . $ijazahBtn . '</td>
                    <td class="text-center">' . $raportBtn . '</td>
                </tr>';
            }
        }

        return $html;
    }
}