<?php

namespace App\Http\Controllers;

use App\Models\DataMaster\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        $staffsAktif    = Staff::where('status', 'aktif')->latest()->get();
        $staffsNonaktif = Staff::where('status', 'nonaktif')->latest('tanggal_nonaktif')->get();

        return view('admin.data_master.staff', compact('staffsAktif', 'staffsNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_staff' => 'required|string|max:255',
            'jabatan'    => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'no_wa'      => 'required|string|max:20',
            'alamat'     => 'required|string',
        ]);

        Staff::create([
            'nama_staff' => $request->nama_staff,
            'jabatan'    => $request->jabatan,
            'email'      => $request->email,
            'no_wa'      => $request->no_wa,
            'alamat'     => $request->alamat,
            'status'     => 'aktif',
        ]);

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_staff' => 'required|string|max:255',
            'jabatan'    => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'no_wa'      => 'required|string|max:20',
            'alamat'     => 'required|string',
        ]);

        $staff = Staff::findOrFail($id);
        $staff->update($request->only(['nama_staff', 'jabatan', 'email', 'no_wa', 'alamat']));

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui!');
    }

    /**
     * Nonaktifkan staff (bukan delete permanen).
     */
    public function destroy($id)
    {
        try {
            $staff  = Staff::findOrFail($id);
            $alasan = request('alasan_nonaktif');
            $suratPath = null;

            if (request()->hasFile('surat_keterangan')) {
                $file      = request()->file('surat_keterangan');
                $fileName  = time() . '_surat_staff_' . $staff->id . '.' . $file->getClientOriginalExtension();
                $suratPath = $file->storeAs('nonaktif_staff', $fileName, 'local');
            }

            $staff->update([
                'status'           => 'nonaktif',
                'alasan_nonaktif'  => $alasan,
                'surat_keterangan' => $suratPath,
                'tanggal_nonaktif' => now(),
            ]);

            return redirect()->route('staff.index')->with('success', 'Staff berhasil dipindahkan ke data nonaktif.');
        } catch (\Exception $e) {
            Log::error('Staff Nonaktif Error: ' . $e->getMessage());
            return redirect()->route('staff.index')->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Pulihkan staff nonaktif kembali ke aktif.
     */
    public function restore($id)
    {
        $staff = Staff::findOrFail($id);

        if ($staff->surat_keterangan && Storage::disk('local')->exists($staff->surat_keterangan)) {
            Storage::disk('local')->delete($staff->surat_keterangan);
        }

        $staff->update([
            'status'           => 'aktif',
            'alasan_nonaktif'  => null,
            'surat_keterangan' => null,
            'tanggal_nonaktif' => null,
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff berhasil dipulihkan ke data aktif.');
    }

    /**
     * Download surat keterangan nonaktif staff.
     */
    public function downloadSurat($id)
    {
        $staff = Staff::findOrFail($id);

        if (!$staff->surat_keterangan || !Storage::disk('local')->exists($staff->surat_keterangan)) {
            abort(404, 'File surat tidak ditemukan.');
        }

        return Storage::disk('local')->download(
            $staff->surat_keterangan,
            'Surat_Keterangan_' . str_replace(' ', '_', $staff->nama_staff) . '.' . pathinfo($staff->surat_keterangan, PATHINFO_EXTENSION)
        );
    }

    /**
     * AJAX Search — sadar tab (aktif / nonaktif).
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        $tab    = $request->get('tab', 'aktif');
        $html   = '';

        $staffs = Staff::where('status', $tab === 'nonaktif' ? 'nonaktif' : 'aktif')
            ->where(function ($q) use ($search) {
                $q->where('nama_staff', 'LIKE', "%{$search}%")
                  ->orWhere('jabatan', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('no_wa', 'LIKE', "%{$search}%");
            })
            ->get();

        if ($staffs->isEmpty()) {
            $cols = $tab === 'nonaktif' ? 8 : 7;
            $html = "<tr><td colspan=\"{$cols}\" class=\"text-center py-4 text-muted\">Data staff tidak ditemukan.</td></tr>";
        } else {
            foreach ($staffs as $index => $s) {
                $safeNama    = addslashes($s->nama_staff);
                $safeJabatan = addslashes($s->jabatan);
                $safeEmail   = addslashes($s->email);
                $safeWa      = addslashes($s->no_wa);
                $safeAlamat  = addslashes($s->alamat);

                if ($tab === 'nonaktif') {
                    $suratBtn = $s->surat_keterangan
                        ? '<a href="' . route('staff.download-surat', $s->id) . '" class="btn btn-sm btn-outline-secondary border-0" title="Download Surat"><i class="bi bi-file-earmark-arrow-down"></i></a>'
                        : '';

                    $csrf         = csrf_field();
                    $restoreRoute = route('staff.restore', $s->id);
                    $tglNonaktif  = $s->tanggal_nonaktif
                        ? \Carbon\Carbon::parse($s->tanggal_nonaktif)->format('d M Y')
                        : '-';

                    $html .= "<tr>
                        <td>" . ($index + 1) . "</td>
                        <td class='fw-bold'>" . e($s->nama_staff) . "</td>
                        <td>" . e($s->jabatan) . "</td>
                        <td>" . e($s->email) . "</td>
                        <td>" . e($s->no_wa) . "</td>
                        <td><span class='badge bg-danger bg-opacity-10 text-danger px-2'>" . e($s->alasan_nonaktif ?? '-') . "</span></td>
                        <td class='text-muted small'>{$tglNonaktif}</td>
                        <td class='text-center'>
                            {$suratBtn}
                            <form action='{$restoreRoute}' method='POST' class='d-inline'>
                                {$csrf}
                                <button class='btn btn-sm btn-outline-success border-0' title='Pulihkan'
                                        onclick=\"return confirm('Pulihkan staff ini ke data aktif?')\">
                                    <i class='bi bi-arrow-counterclockwise'></i>
                                </button>
                            </form>
                        </td>
                    </tr>";
                } else {
                    $alamatLimit  = Str::limit($s->alamat, 40);
                    $html .= "<tr>
                        <td>" . ($index + 1) . "</td>
                        <td class='fw-bold'>" . e($s->nama_staff) . "</td>
                        <td>" . e($s->jabatan) . "</td>
                        <td>" . e($s->email) . "</td>
                        <td>" . e($s->no_wa) . "</td>
                        <td>" . e($alamatLimit) . "</td>
                        <td class='text-center'>
                            <div class='btn-group'>
                                <button class='btn btn-sm btn-outline-success border-0'
                                    onclick=\"openEditModal('{$s->id}', '{$safeNama}', '{$safeJabatan}', '{$safeEmail}', '{$safeWa}', '{$safeAlamat}')\">
                                    <i class='bi bi-pencil-square'></i>
                                </button>
                                <button class='btn btn-sm btn-outline-danger border-0'
                                    onclick=\"bukaModalNonaktif({$s->id}, '{$safeNama}')\">
                                    <i class='bi bi-trash'></i>
                                </button>
                            </div>
                        </td>
                    </tr>";
                }
            }
        }

        return $html;
    }
}
