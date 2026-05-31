<?php

namespace App\Http\Controllers;

use App\Models\OrtuMurid;
use App\Models\Murid;
use Illuminate\Http\Request;

class OrtuMuridController extends Controller
{
    public function index()
    {
        // Load data murid beserta ortu muridnya
        $data = Murid::with('ortu')->has('ortu')->get();
        return view('dashboard_admin.ortu_murid', compact('data'));
    }

    public function search(Request $request)
    {
        $output = "";
        $query = $request->get('search');

        // Pencarian menggunakan Eloquent dengan relasi
        $results = Murid::with('ortu')
            ->whereHas('ortu', function($q) use ($query) {
                $q->where('nama_ayah', 'LIKE', '%' . $query . '%')
                  ->orWhere('nama_ibu', 'LIKE', '%' . $query . '%');
            })
            ->orWhere('nama_lengkap', 'LIKE', '%' . $query . '%')
            ->orWhere('no_hp', 'LIKE', '%' . $query . '%')
            ->get();

        if ($results->count() > 0) {
            foreach ($results as $row) {
                $output .= '
                <tr>
                    <td class="fw-bold text-dark">' . $row->nama_lengkap . '</td>
                    <td>' . ($row->ortu->nama_ayah ?? '-') . '</td>
                    <td>' . ($row->ortu->nama_ibu ?? '-') . '</td>
                    <td><span class="badge bg-success bg-opacity-10 text-success px-3">' . $row->no_hp . '</span></td>
                </tr>';
            }
        } else {
            $output = '<tr><td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>';
        }

        return response($output);
    }
}