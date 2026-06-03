<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    /**
     * Menampilkan semua data staff
     */
    public function index()
    {
        $staffs = Staff::all();
        // Mengarahkan ke folder views/dashboard_admin/staff.blade.php sesuai struktur guru
        return view('dashboard_admin.staff', compact('staffs'));
    }

    /**
     * Menyimpan data staff baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_staff' => 'required|string|max:255',
            'jabatan'    => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'no_wa'      => 'required|string|max:20',
            'alamat'     => 'required|string',
        ]);

        $staff = new Staff();
        $staff->nama_staff = $request->nama_staff;
        $staff->jabatan    = $request->jabatan;
        $staff->email      = $request->email;
        $staff->no_wa      = $request->no_wa;
        $staff->alamat     = $request->alamat;
        $staff->save();

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil ditambahkan!');
    }

    /**
     * Memperbarui data staff melalui Modal Edit
     */
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
        $staff->nama_staff = $request->nama_staff;
        $staff->jabatan    = $request->jabatan;
        $staff->email      = $request->email;
        $staff->no_wa      = $request->no_wa;
        $staff->alamat     = $request->alamat;
        $staff->save();

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui!');
    }

    /**
     * Menghapus data staff
     */
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil dihapus!');
    }

    /**
     * Fitur Pencarian AJAX Real-time
     */
    public function search(Request $request)
    {
        $search = $request->get('search');
        
        $staffs = Staff::where(function($query) use ($search) {
            $query->where('nama_staff', 'LIKE', "%{$search}%")
                  ->orWhere('jabatan', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('no_wa', 'LIKE', "%{$search}%");
        })->get();

        $html = '';
        if ($staffs->isEmpty()) {
            $html .= '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data staff yang cocok.</td></tr>';
        } else {
            foreach ($staffs as $index => $s) {
                $alamatLimit = Str::limit($s->alamat, 40);
                $csrf = csrf_field();
                $method = method_field('DELETE');
                $deleteRoute = route('staff.destroy', $s->id);
                
                // Escape string javascript untuk mengantisipasi tanda petik tunggal (') di database
                $safeNama   = addslashes($s->nama_staff);
                $safeJabatan = addslashes($s->jabatan);
                $safeEmail   = addslashes($s->email);
                $safeWa      = addslashes($s->no_wa);
                $safeAlamat  = addslashes($s->alamat);

                $html .= "<tr>
                    <td>" . ($index + 1) . "</td>
                    <td class='fw-bold'>" . e($s->nama_staff) . "</td>
                    <td>" . e($s->jabatan) . "</td>
                    <td>" . e($s->email) . "</td>
                    <td>" . e($s->no_wa) . "</td>
                    <td>" . e($alamatLimit) . "</td>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <button class='btn btn-sm btn-outline-primary border-0' 
                                onclick=\"openEditModal('{$s->id}', '{$safeNama}', '{$safeJabatan}', '{$safeEmail}', '{$safeWa}', '{$safeAlamat}')\">
                                <i class='bi bi-pencil-square'></i>
                            </button>
                            <form action='{$deleteRoute}' method='POST' onsubmit=\"return confirm('Hapus data staff ini?')\">
                                {$csrf} {$method}
                                <button type='submit' class='btn btn-sm btn-outline-danger border-0'>
                                    <i class='bi bi-trash'></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>";
            }
        }
        return $html;
    }
}