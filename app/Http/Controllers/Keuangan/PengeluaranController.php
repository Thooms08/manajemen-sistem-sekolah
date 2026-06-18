<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Traits\RendersUserView;


use App\Http\Controllers\Controller;
use App\Models\Keuangan\Pengeluaran;
use App\Models\Keuangan\BuktiPengeluaran;
use App\Models\Informasi\ProfileSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class PengeluaranController extends Controller
{
    use RendersUserView;
    // ──────────────────────────────────────────────────────────────────
    // Helper: bangun query berdasarkan filter range waktu
    // ──────────────────────────────────────────────────────────────────
    private function buildRangeQuery(Request $request, string $status)
    {
        $range    = $request->get('range', '1bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = Pengeluaran::where('status', $status)->latest();

        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
        } else {
            $startDate = match ($range) {
                '1hari'   => Carbon::now()->startOfDay(),
                '1minggu' => Carbon::now()->subWeek()->startOfDay(),
                '1bulan'  => Carbon::now()->subMonth()->startOfDay(),
                '1tahun'  => Carbon::now()->subYear()->startOfDay(),
                default   => Carbon::now()->subMonth()->startOfDay(),
            };
            $query->where('created_at', '>=', $startDate);
        }

        return $query;
    }

    /**
     * Halaman utama: laporan pengeluaran dengan tab Tersedia / Dihapus.
     */
    public function index(Request $request)
    {
        $sekolah  = ProfileSekolah::first();
        $range    = $request->get('range', '1bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $tab      = $request->get('tab', 'tersedia');

        if ($dateFrom && $dateTo) {
            $range = 'custom';
        }

        // ── Query data ─────────────────────────────────────────────
        $pengeluarans = $this->buildRangeQuery($request, 'tersedia')
            ->with('buktiPengeluaran')
            ->get();

        $dihapusList  = $this->buildRangeQuery($request, 'dihapus')
            ->with('buktiPengeluaran')
            ->get();

        // ── Summary ────────────────────────────────────────────────
        $totalNominal   = $pengeluarans->sum('total');
        $totalAktifitas = $pengeluarans->count();

        return $this->renderView('admin.keuangan.pengeluaran', compact(
            'sekolah',
            'pengeluarans', 'dihapusList',
            'totalNominal', 'totalAktifitas',
            'range', 'dateFrom', 'dateTo', 'tab'
        ));
    }

    /**
     * Simpan satu atau banyak pengeluaran sekaligus.
     * Input: array rows[] masing-masing berisi field pengeluaran.
     * Foto bukti di-upload per-pengeluaran: bukti_foto[<index>][]
     */
    public function store(Request $request)
    {
        $request->validate([
            'rows'                    => 'required|array|min:1',
            'rows.*.jenis_pengeluaran'=> 'required|string|in:operasional,gaji_staff,gaji_guru,lainnya',
            'rows.*.nama_pengeluaran' => 'required|string|max:255',
            'rows.*.nominal'          => 'required|integer|min:1',
            'rows.*.qty'              => 'required|integer|min:1',
            'rows.*.total'            => 'required|integer|min:1',
        ]);

        foreach ($request->rows as $idx => $row) {
            // Validasi tambahan: jenis lainnya wajib isi keterangan
            if (($row['jenis_pengeluaran'] ?? '') === 'lainnya') {
                if (empty($row['keterangan_lainnya'])) {
                    return back()
                        ->withInput()
                        ->withErrors(["rows.{$idx}.keterangan_lainnya" => "Keterangan jenis wajib diisi untuk jenis 'Lainnya'."]);
                }
            }

            $total = (int) $row['nominal'] * (int) $row['qty'];

            $pengeluaran = Pengeluaran::create([
                'jenis_pengeluaran'  => $row['jenis_pengeluaran'],
                'keterangan_lainnya' => ($row['jenis_pengeluaran'] === 'lainnya') ? ($row['keterangan_lainnya'] ?? null) : null,
                'nama_pengeluaran'   => $row['nama_pengeluaran'],
                'deskripsi'          => $row['deskripsi'] ?? null,
                'nominal'            => (int) $row['nominal'],
                'qty'                => (int) $row['qty'],
                'total'              => $total,
                'status'             => 'tersedia',
            ]);

            // ── Upload bukti foto (bisa banyak per pengeluaran) ────────
            $fotoKey = "bukti_foto.{$idx}";
            if ($request->hasFile($fotoKey)) {
                foreach ($request->file($fotoKey) as $foto) {
                    if ($foto->isValid()) {
                        $path = $foto->storeAs(
                            'bukti_pengeluaran',
                            time() . '_' . $foto->getClientOriginalName(),
                            'local'         // simpan di storage/app/bukti_pengeluaran
                        );
                        BuktiPengeluaran::create([
                            'id_pengeluaran' => $pengeluaran->id,
                            'bukti_foto'     => $path,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('keuangan.pengeluaran.index')
            ->with('success', 'Data pengeluaran berhasil disimpan.');
    }

    /**
     * AJAX: Ambil data pengeluaran untuk form edit.
     */
    public function getEditData($id)
    {
        $pengeluaran = Pengeluaran::with('buktiPengeluaran')->findOrFail($id);

        $buktiList = $pengeluaran->buktiPengeluaran->map(function ($b) {
            return [
                'id'  => $b->id,
                'url' => route('keuangan.pengeluaran.bukti', $b->id),
            ];
        });

        return response()->json([
            'id'                 => $pengeluaran->id,
            'jenis_pengeluaran'  => $pengeluaran->jenis_pengeluaran,
            'keterangan_lainnya' => $pengeluaran->keterangan_lainnya,
            'nama_pengeluaran'   => $pengeluaran->nama_pengeluaran,
            'deskripsi'          => $pengeluaran->deskripsi,
            'nominal'            => $pengeluaran->nominal,
            'qty'                => $pengeluaran->qty,
            'total'              => $pengeluaran->total,
            'bukti'              => $buktiList,
        ]);
    }

    /**
     * Update data pengeluaran (single row edit).
     * Foto lama yang dihapus dikirim via deleted_bukti_ids[].
     * Foto baru di-upload via bukti_foto_baru[].
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $request->validate([
            'jenis_pengeluaran'  => 'required|string|in:operasional,gaji_staff,gaji_guru,lainnya',
            'nama_pengeluaran'   => 'required|string|max:255',
            'nominal'            => 'required|integer|min:1',
            'qty'                => 'required|integer|min:1',
            'total'              => 'required|integer|min:1',
            'bukti_foto_baru.*'  => 'nullable|image|max:2048',
        ]);

        if ($request->jenis_pengeluaran === 'lainnya' && empty($request->keterangan_lainnya)) {
            return back()->withInput()
                ->withErrors(['keterangan_lainnya' => "Keterangan jenis wajib diisi untuk jenis 'Lainnya'."]);
        }

        // ── Hapus foto yang ditandai dihapus ───────────────────────
        if ($request->filled('deleted_bukti_ids')) {
            $ids = explode(',', $request->deleted_bukti_ids);
            foreach ($ids as $buktiId) {
                $buktiId = (int) trim($buktiId);
                if ($buktiId > 0) {
                    $bukti = BuktiPengeluaran::where('id', $buktiId)
                        ->where('id_pengeluaran', $id)
                        ->first();
                    if ($bukti) {
                        if ($bukti->bukti_foto) {
                            Storage::disk('local')->delete($bukti->bukti_foto);
                        }
                        $bukti->delete();
                    }
                }
            }
        }

        // ── Upload foto baru ───────────────────────────────────────
        if ($request->hasFile('bukti_foto_baru')) {
            foreach ($request->file('bukti_foto_baru') as $foto) {
                if ($foto->isValid()) {
                    $path = $foto->storeAs(
                        'bukti_pengeluaran',
                        time() . '_' . $foto->getClientOriginalName(),
                        'local'
                    );
                    BuktiPengeluaran::create([
                        'id_pengeluaran' => $pengeluaran->id,
                        'bukti_foto'     => $path,
                    ]);
                }
            }
        }

        // ── Update data utama ──────────────────────────────────────
        $total = (int) $request->nominal * (int) $request->qty;
        $pengeluaran->update([
            'jenis_pengeluaran'  => $request->jenis_pengeluaran,
            'keterangan_lainnya' => ($request->jenis_pengeluaran === 'lainnya') ? $request->keterangan_lainnya : null,
            'nama_pengeluaran'   => $request->nama_pengeluaran,
            'deskripsi'          => $request->deskripsi ?? null,
            'nominal'            => (int) $request->nominal,
            'qty'                => (int) $request->qty,
            'total'              => $total,
            'edited_at'          => Carbon::now(),
        ]);

        return redirect()->route('keuangan.pengeluaran.index', ['tab' => 'tersedia'])
            ->with('success', 'Data pengeluaran berhasil diperbarui.');
    }

    /**
     * Soft-delete: ubah status menjadi 'dihapus'.
     */
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update(['status' => 'dihapus']);

        return redirect()->route('keuangan.pengeluaran.index', ['tab' => 'tersedia'])
            ->with('success', 'Data pengeluaran dipindahkan ke tab Dihapus.');
    }

    /**
     * Pulihkan dari tab Dihapus ke Tersedia.
     */
    public function restore($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update(['status' => 'tersedia']);

        return redirect()->route('keuangan.pengeluaran.index', ['tab' => 'dihapus'])
            ->with('success', 'Data pengeluaran berhasil dipulihkan.');
    }

    /**
     * Serve foto bukti secara aman (private storage).
     */
    public function viewBukti(Request $request, $id)
    {
        $bukti = BuktiPengeluaran::findOrFail($id);

        abort_unless(
            Storage::disk('local')->exists($bukti->bukti_foto),
            404,
            'File tidak ditemukan.'
        );

        $path = Storage::disk('local')->path($bukti->bukti_foto);
        $ext  = strtolower(pathinfo($bukti->bukti_foto, PATHINFO_EXTENSION));

        $mimeMap = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
        ];

        $mime = $mimeMap[$ext] ?? 'application/octet-stream';

        return response()->file($path, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Export Excel — hanya data berstatus 'tersedia'.
     */
    public function exportExcel(Request $request)
    {
        $range    = $request->get('range', '1bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = Pengeluaran::where('status', 'tersedia')->latest();

        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
        } else {
            $startDate = match ($range) {
                '1hari'   => Carbon::now()->startOfDay(),
                '1minggu' => Carbon::now()->subWeek()->startOfDay(),
                '1bulan'  => Carbon::now()->subMonth()->startOfDay(),
                '1tahun'  => Carbon::now()->subYear()->startOfDay(),
                default   => Carbon::now()->subMonth()->startOfDay(),
            };
            $query->where('created_at', '>=', $startDate);
        }

        $pengeluarans = $query->get();

        $jenisMap = [
            'operasional' => 'Operasional',
            'gaji_staff'  => 'Gaji Staff',
            'gaji_guru'   => 'Gaji Guru',
            'lainnya'     => 'Lainnya',
        ];

        $rows = $pengeluarans->map(function ($p, $index) use ($jenisMap) {
            return [
                'No'              => $index + 1,
                'Tanggal'         => Carbon::parse($p->created_at)->format('d/m/Y'),
                'Jenis Pengeluaran' => $jenisMap[$p->jenis_pengeluaran] ?? $p->jenis_pengeluaran,
                'Keterangan Jenis'  => $p->keterangan_lainnya ?? '-',
                'Nama Pengeluaran'  => $p->nama_pengeluaran,
                'Deskripsi'         => $p->deskripsi ?? '-',
                'Nominal (Rp)'      => (int) $p->nominal,
                'QTY'               => (int) $p->qty,
                'Total (Rp)'        => (int) $p->total,
            ];
        });

        $filename = 'laporan_pengeluaran_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return (new FastExcel($rows))->download($filename);
    }
}
