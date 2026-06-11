<?php

namespace App\Http\Controllers;

use App\Models\DataMaster\OrtuMurid;
use App\Models\DataMaster\Murid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrtuMuridController extends Controller
{
    public function index()
    {
        // Tab Aktif: murid dengan status konfirmasi yang memiliki data ortu
        $dataAktif = Murid::with('ortu')
            ->has('ortu')
            ->where('status', 'konfirmasi')
            ->get();

        // Tab Nonaktif: murid dengan status nonaktif yang memiliki data ortu
        $dataNonaktif = Murid::with('ortu')
            ->has('ortu')
            ->where('status', 'nonaktif')
            ->get();

        return view('admin.data_master.ortu_murid', compact('dataAktif', 'dataNonaktif'));
    }

    public function search(Request $request)
    {
        $query = $request->get('search');
        $tab   = $request->get('tab', 'aktif');

        $muridQuery = Murid::with('ortu')
            ->has('ortu')
            ->where('status', $tab === 'nonaktif' ? 'nonaktif' : 'konfirmasi')
            ->where(function ($q) use ($query) {
                $q->where('nama_lengkap', 'LIKE', '%' . $query . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('ortu', function ($q2) use ($query) {
                      $q2->where('nama_ayah', 'LIKE', '%' . $query . '%')
                         ->orWhere('nama_ibu', 'LIKE', '%' . $query . '%');
                  });
            });

        $results = $muridQuery->get();

        $output = '';
        if ($results->count() > 0) {
            foreach ($results as $row) {
                if ($tab === 'nonaktif') {
                    $output .= '
                    <tr>
                        <td class="fw-bold text-dark">' . e($row->nama_lengkap) . '</td>
                        <td>' . e($row->ortu->nama_ayah ?? '-') . '</td>
                        <td>' . e($row->ortu->nama_ibu ?? '-') . '</td>
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
                        <td>' . e($row->ortu->nama_ayah ?? '-') . '</td>
                        <td>' . e($row->ortu->nama_ibu ?? '-') . '</td>
                        <td>
                            <span class="badge bg-success bg-opacity-10 text-success px-3">
                                ' . e($row->no_hp ?? '-') . '
                            </span>
                        </td>
                    </tr>';
                }
            }
        } else {
            $cols = $tab === 'nonaktif' ? 5 : 4;
            $output = '<tr><td colspan="' . $cols . '" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }
}