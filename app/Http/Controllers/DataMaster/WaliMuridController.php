<?php

namespace App\Http\Controllers\DataMaster;

use App\Models\DataMaster\WaliMurid;
use App\Models\DataMaster\Murid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WaliMuridController extends Controller
{
    public function index()
    {
        // Tab Aktif: murid dengan status konfirmasi yang memiliki wali
        $dataAktif = Murid::with('wali')
            ->has('wali')
            ->where('status', 'konfirmasi')
            ->get();

        // Tab Nonaktif: murid dengan status nonaktif yang memiliki wali
        $dataNonaktif = Murid::with('wali')
            ->has('wali')
            ->where('status', 'nonaktif')
            ->get();

        return view('admin.data_master.wali_murid', compact('dataAktif', 'dataNonaktif'));
    }

    public function search(Request $request)
    {
        $query = $request->get('search');
        $tab   = $request->get('tab', 'aktif');

        $muridQuery = Murid::with('wali')
            ->has('wali')
            ->where('status', $tab === 'nonaktif' ? 'nonaktif' : 'konfirmasi')
            ->where(function ($q) use ($query) {
                $q->where('nama_lengkap', 'LIKE', '%' . $query . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('wali', function ($q2) use ($query) {
                      $q2->where('nama_wali', 'LIKE', '%' . $query . '%')
                         ->orWhere('hubungan_wali', 'LIKE', '%' . $query . '%')
                         ->orWhere('pekerjaan_wali', 'LIKE', '%' . $query . '%');
                  });
            });

        $results = $muridQuery->get();

        $output = '';
        if ($results->count() > 0) {
            foreach ($results as $row) {
                $wali = $row->wali;
                if ($tab === 'nonaktif') {
                    $output .= '
                    <tr>
                        <td class="fw-bold text-dark">' . e($row->nama_lengkap) . '</td>
                        <td>' . e($wali->nama_wali ?? '-') . '</td>
                        <td>' . e($wali->hubungan_wali ?? '-') . '</td>
                        <td>' . e($wali->pekerjaan_wali ?? '-') . '</td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3">
                                ' . e($row->no_hp ?? '-') . '
                            </span>
                        </td>
                        <td><span class="badge bg-danger bg-opacity-10 text-danger px-2">Nonaktif</span></td>
                    </tr>';
                } else {
                    $output .= '
                    <tr>
                        <td class="fw-bold text-dark">' . e($row->nama_lengkap) . '</td>
                        <td>' . e($wali->nama_wali ?? '-') . '</td>
                        <td>' . e($wali->hubungan_wali ?? '-') . '</td>
                        <td>' . e($wali->pekerjaan_wali ?? '-') . '</td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success px-3">
                                ' . e($row->no_hp ?? '-') . '
                            </span>
                        </td>
                    </tr>';
                }
            }
        } else {
            $cols = $tab === 'nonaktif' ? 6 : 5;
            $output = '<tr><td colspan="' . $cols . '" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }
}
