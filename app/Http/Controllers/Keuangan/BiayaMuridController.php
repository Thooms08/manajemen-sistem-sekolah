<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Traits\RendersUserView;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Keuangan\BiayaMurid;
use App\Models\Keuangan\AkunPembayaran;
use App\Models\Informasi\ProfileSekolah;

class BiayaMuridController extends Controller
{
    use RendersUserView;
    public function index()
    {
        $sekolah  = ProfileSekolah::first();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $biayas   = BiayaMurid::with('account')->orderBy('id')->get();

        return $this->renderView('admin.keuangan.biaya-murid', compact('sekolah', 'accounts', 'biayas'));
    }

    /**
     * Simpan satu biaya baru.
     * Input: name, amount, account_id (nullable = cash/tunai)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:191|unique:keuangan_db.biaya_murid,name',
            'amount'     => 'required|numeric|min:1',
            'account_id' => 'nullable|integer|exists:keuangan_db.akun_pembayaran,id',
        ], [
            'name.unique' => 'Nama biaya ini sudah terdaftar.',
            'amount.min'  => 'Nominal harus lebih dari 0.',
        ]);

        BiayaMurid::create([
            'name'       => $validated['name'],
            'amount'     => $validated['amount'],
            'account_id' => $validated['account_id'] ?? null,
            'is_active'  => true,
        ]);

        return redirect()->route('biaya-murid.index')
            ->with('success', 'Biaya "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Update biaya yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $biaya = BiayaMurid::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:191|unique:keuangan_db.biaya_murid,name,' . $id . ',id',
            'amount'     => 'required|numeric|min:0',
            'account_id' => 'nullable|integer|exists:keuangan_db.akun_pembayaran,id',
        ], [
            'name.unique' => 'Nama biaya ini sudah digunakan oleh biaya lain.',
        ]);

        $biaya->update([
            'name'       => $validated['name'],
            'amount'     => $validated['amount'],
            'account_id' => $validated['account_id'] ?? null,
        ]);

        return redirect()->route('biaya-murid.index')
            ->with('success', 'Biaya "' . $validated['name'] . '" berhasil diperbarui.');
    }

    /**
     * Hapus biaya.
     */
    public function destroy($id)
    {
        $biaya = BiayaMurid::findOrFail($id);
        $nama  = $biaya->name;
        $biaya->delete();

        return redirect()->route('biaya-murid.index')
            ->with('success', 'Biaya "' . $nama . '" berhasil dihapus.');
    }

    /**
     * AJAX: cek apakah nama biaya sudah ada (untuk validasi realtime di form tambah).
     */
    public function checkFeeName(Request $request)
    {
        $name      = trim($request->input('name', ''));
        $excludeId = $request->input('exclude_id'); // untuk mode edit

        $query = BiayaMurid::where('name', $name);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return response()->json(['exists' => $query->exists()]);
    }
}
