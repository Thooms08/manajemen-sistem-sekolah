<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keuangan\AkunPembayaran;
use App\Models\Informasi\ProfileSekolah;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class AkunPembayaranController extends Controller
{
    public function index()
    {
        $sekolah = ProfileSekolah::first();
        $accounts = AkunPembayaran::orderBy('id','desc')->get();
        return view('admin.keuangan.akun-pembayaran', compact('sekolah','accounts'));
    }

    public function store(Request $request)
    {
        $isQris = $request->has('is_qris');

        $rules = [
            'bank_name' => 'required|string|max:191',
        ];

        if ($isQris) {
            $rules['qris_image'] = 'required|image|max:2048';
        } else {
            $rules['account_number'] = 'required|string|max:100|unique:keuangan_db.akun_pembayaran,account_number';
            $rules['account_holder'] = 'required|string|max:191';
        }

        $validated = $request->validate($rules);

        $data = [
            'bank_name' => $validated['bank_name'],
            'is_qris' => $isQris ? 1 : 0,
        ];

        if ($isQris && $request->hasFile('qris_image')) {
            $file = $request->file('qris_image');
            $dir = public_path('assets/akun-pembayaran/qris');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move($dir, $filename);
            $path = 'assets/akun-pembayaran/qris/' . $filename;
            $data['qris_image'] = $path;
            $data['account_number'] = null;
            $data['account_holder'] = null;
        } else {
            $data['account_number'] = $validated['account_number'];
            $data['account_holder'] = $validated['account_holder'];
            $data['qris_image'] = null;
        }

        AkunPembayaran::create($data);

        return redirect()->route('akun-pembayaran.index')->with('success', 'Akun pembayaran berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $account = AkunPembayaran::findOrFail($id);
        $isQris = $request->has('is_qris');

        $rules = [
            'bank_name' => 'required|string|max:191',
        ];

        if ($isQris) {
            $rules['qris_image'] = 'nullable|image|max:2048';
        } else {
            $rules['account_number'] = 'required|string|max:100|unique:keuangan_db.akun_pembayaran,account_number,' . $id . ',id';
            $rules['account_holder'] = 'required|string|max:191';
        }

        $validated = $request->validate($rules);

        $account->bank_name = $validated['bank_name'];
        $account->is_qris = $isQris ? 1 : 0;

        if ($isQris) {
            if ($request->hasFile('qris_image')) {
                // delete old file
                if ($account->qris_image && File::exists(public_path($account->qris_image))) {
                    File::delete(public_path($account->qris_image));
                }
                $file = $request->file('qris_image');
                $dir = public_path('assets/akun-pembayaran/qris');
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move($dir, $filename);
                $path = 'assets/akun-pembayaran/qris/' . $filename;
                $account->qris_image = $path;
            }
            $account->account_number = null;
            $account->account_holder = null;
        } else {
            $account->account_number = $validated['account_number'];
            $account->account_holder = $validated['account_holder'];
            // if there was previously a qris image, delete it
            if ($account->qris_image && File::exists(public_path($account->qris_image))) {
                File::delete(public_path($account->qris_image));
                $account->qris_image = null;
            }
        }

        $account->save();

        return redirect()->route('akun-pembayaran.index')->with('success', 'Akun pembayaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $account = AkunPembayaran::findOrFail($id);
        if ($account->qris_image && File::exists(public_path($account->qris_image))) {
            File::delete(public_path($account->qris_image));
        }
        $account->delete();
        return redirect()->route('akun-pembayaran.index')->with('success', 'Akun pembayaran berhasil dihapus');
    }

    public function checkNumber(Request $request)
    {
        $number = $request->get('account_number');
        $exclude = $request->get('exclude_id');

        if (!$number) {
            return response()->json(['exists' => false]);
        }

        $query = AkunPembayaran::where('account_number', $number);
        if ($exclude) {
            $query->where('id', '<>', $exclude);
        }
        $exists = $query->exists();

        if ($exists) {
            return response()->json([
                'exists' => true,
                'message' => 'Nomor rekening ini sudah terdaftar. Silakan gunakan nomor rekening lain.'
            ]);
        }

        return response()->json(['exists' => false]);
    }
}
