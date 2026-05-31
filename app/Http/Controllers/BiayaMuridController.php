<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiayaMurid;
use App\Models\ProfileSekolah;
use App\Models\AkunPembayaran;

class BiayaMuridController extends Controller
{
    public function index()
    {
        $sekolah = ProfileSekolah::first();
        $accounts = AkunPembayaran::orderBy('bank_name')->get();
        $biayas = BiayaMurid::orderBy('id')->get();
        return view('dashboard_admin.biaya-murid', compact('sekolah','accounts','biayas'));
    }

    public function store(Request $request)
    {
        // Expect arrays: fee_name[], fee_amount[], fee_account[]
        $names = $request->input('fee_name', []);
        $amounts = $request->input('fee_amount', []);
        $accounts = $request->input('fee_account', []);

        // Get existing fee names to check for duplicates
        $existingFeeNames = BiayaMurid::pluck('name')->toArray();

        $savedCount = 0;
        $duplicateCount = 0;
        
        foreach ($names as $i => $n) {
            $n = trim($n);
            $amount = isset($amounts[$i]) ? $amounts[$i] : 0;
            
            // Only save if name is not empty AND amount is not zero/empty
            if ($n !== '' && $amount !== '' && $amount !== '0' && $amount > 0) {
                // Check if fee name already exists
                if (in_array($n, $existingFeeNames)) {
                    $duplicateCount++;
                    continue; // Skip this fee, don't save
                }
                
                BiayaMurid::create([
                    'name' => $n,
                    'amount' => $amount,
                    'account_id' => isset($accounts[$i]) && $accounts[$i] ? $accounts[$i] : null,
                ]);
                $savedCount++;
            }
        }

        if ($savedCount > 0) {
            $message = 'Nominal biaya berhasil disimpan';
            if ($duplicateCount > 0) {
                $message .= ". {$duplicateCount} biaya dilewati karena sudah ada sebelumnya.";
            }
            return redirect()->route('biaya-murid.index')->with('success', $message);
        } else if ($duplicateCount > 0) {
            return redirect()->route('biaya-murid.index')->with('error', 'Semua biaya yang Anda input sudah ada sebelumnya. Silakan hapus biaya yang sudah ada atau input nama biaya lain.');
        } else {
            return redirect()->route('biaya-murid.index')->with('error', 'Tidak ada biaya yang disimpan. Pastikan Anda mengisi nama dan nominal biaya.');
        }
    }

    public function checkFeeName(Request $request)
    {
        $name = $request->input('name');
        $exists = BiayaMurid::where('name', $name)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function update(Request $request, $id)
    {
        $b = BiayaMurid::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0',
            'account_id' => 'nullable|integer',
            'is_cash' => 'nullable',
        ]);
        
        // If is_cash is checked, set account_id to null
        if (isset($validated['is_cash'])) {
            $validated['account_id'] = null;
        }
        
        // Remove is_cash from validated array as it's not a column in the database
        unset($validated['is_cash']);
        
        $b->update($validated);
        return redirect()->route('biaya-murid.index')->with('success', 'Biaya berhasil diperbarui');
    }

    public function destroy($id)
    {
        $b = BiayaMurid::findOrFail($id);
        $b->delete();
        return redirect()->route('biaya-murid.index')->with('success', 'Biaya berhasil dihapus');
    }
}
