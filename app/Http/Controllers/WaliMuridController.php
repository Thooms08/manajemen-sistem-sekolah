<?php

namespace App\Http\Controllers;

use App\Models\WaliMurid;
use App\Models\Murid;
use Illuminate\Http\Request;

class WaliMuridController extends Controller
{
    public function index()
    {
        // Load murid yang memiliki data wali
        $data = Murid::with('wali')->has('wali')->get();
        return view('admin.data_master.wali_murid', compact('data'));
    }

    public function search(Request $request)
    {
        $query = $request->get('search');

        $results = Murid::with('wali')
            ->where(function ($q) use ($query) {
                $q->where('nama_lengkap', 'LIKE', '%' . $query . '%')
                  ->orWhere('no_hp', 'LIKE', '%' . $query . '%')
                  ->orWhereHas('wali', function ($q2) use ($query) {
                      $q2->where('nama_wali', 'LIKE', '%' . $query . '%')
                         ->orWhere('hubungan_wali', 'LIKE', '%' . $query . '%')
                         ->orWhere('pekerjaan_wali', 'LIKE', '%' . $query . '%');
                  });
            })
            ->whereHas('wali')
            ->get();

        $output = '';
        if ($results->count() > 0) {
            foreach ($results as $row) {
                $wali = $row->wali;
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
        } else {
            $output = '<tr><td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }
}
