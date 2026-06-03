<?php

namespace App\Http\Controllers;

use App\Models\Murid;
use App\Models\Dokumen\Kelulusan;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Tampilan Utama Halaman Alumni
     */
    public function index()
    {
        // Ambil daftar tahun lulus unik langsung dari tabel kelulusan dokumen_db
        $years = Kelulusan::where('status', 'lulus')
            ->whereNotNull('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        // Ambil kumpulan id_murid yang berstatus lulus langsung dari model Kelulusan (Aman dari bentrokan DB)
        $graduatedIds = Kelulusan::where('status', 'lulus')->pluck('id_murid');

        // Tampilkan data murid berdasarkan id siswa yang lulus tersebut
        $alumnis = Murid::whereIn('id', $graduatedIds)
            ->with(['kelas', 'kelulusan'])
            ->get();

        return view('dashboard_admin.alumni', compact('alumnis', 'years'));
    }

    /**
     * Fitur Pencarian & Filter Dropdown AJAX Real-time
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $tahun = $request->get('tahun');

        // Filter awal dari sisi model Kelulusan (dokumen_db)
        $kelulusanQuery = Kelulusan::where('status', 'lulus');
        
        if (!empty($tahun)) {
            $kelulusanQuery->where('tahun_lulus', $tahun);
        }
        
        $graduatedIds = $kelulusanQuery->pluck('id_murid');

        // Query ke model Murid menggunakan hasil filter id_murid di atas dan keyword pencarian
        $alumnis = Murid::whereIn('id', $graduatedIds)
            ->with(['kelas', 'kelulusan'])
            ->where(function($query) use ($search) {
                if (!empty($search)) {
                    $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                          ->orWhere('nisn', 'LIKE', "%{$search}%")
                          ->orWhere('nis_baru', 'LIKE', "%{$search}%");
                }
            })
            ->get();

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