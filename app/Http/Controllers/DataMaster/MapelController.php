<?php

namespace App\Http\Controllers\DataMaster;

use App\Models\DataMaster\Mapel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MapelController extends Controller
{
    public function index()
    {
        $mapels = Mapel::orderBy('created_at', 'desc')->get();
        return view('admin.data_master.mapel', compact('mapels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        Mapel::create($request->all());

        return redirect()->route('mapel.index')->with('success', 'Data mata pelajaran berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        $mapel = Mapel::findOrFail($id);
        $mapel->update($request->all());

        return redirect()->route('mapel.index')->with('success', 'Data mata pelajaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $mapel = Mapel::findOrFail($id);
        $mapel->delete();

        return redirect()->route('mapel.index')->with('success', "Mapel {$mapel->nama_mapel} berhasil dihapus!");
    }

    // Fungsi untuk AJAX Search
    public function search(Request $request)
    {
        $keyword = $request->search;
        $mapels = Mapel::where('nama_mapel', 'like', "%{$keyword}%")
                       ->orWhere('deskripsi', 'like', "%{$keyword}%")
                       ->orderBy('created_at', 'desc')
                       ->get();

        $html = '';
        if ($mapels->count() > 0) {
            foreach ($mapels as $index => $m) {
                // Untuk search AJAX, kita tampilkan semua (tanpa hidden row) agar hasil filter terlihat
                $html .= '<tr>
                    <td>' . ($index + 1) . '</td>
                    <td class="fw-bold">' . htmlspecialchars($m->nama_mapel) . '</td>
                    <td>' . htmlspecialchars(str()->limit($m->deskripsi, 80)) . '</td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-success border-0" title="Edit"
                                onclick="openEditModal(\'' . $m->id . '\', \'' . addslashes($m->nama_mapel) . '\', \'' . addslashes($m->deskripsi) . '\')">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="' . route('mapel.destroy', $m->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus mapel ' . addslashes($m->nama_mapel) . '?\')">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>';
            }
        } else {
            $html .= '<tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-3 d-block mb-2 text-secondary"></i>
                    Data mapel tidak ditemukan.
                </td>
            </tr>';
        }

        return $html;
    }
}