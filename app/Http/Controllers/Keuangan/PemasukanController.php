<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Keuangan\Pemasukan;
use App\Models\Keuangan\BiayaMurid;
use App\Models\Keuangan\AkunPembayaran;
use App\Models\Murid;
use App\Models\ProfileSekolah;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class PemasukanController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // Helper: bangun query berdasarkan filter range waktu
    // ──────────────────────────────────────────────────────────────────
    private function buildRangeQuery(Request $request, string $status)
    {
        $range    = $request->get('range', '1bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = Pemasukan::where('status', $status)->latest();

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

    // ──────────────────────────────────────────────────────────────────
    // Helper: lampirkan data murid ke koleksi pemasukan
    // ──────────────────────────────────────────────────────────────────
    private function attachMurid($collection)
    {
        $ids    = $collection->pluck('id_murid')->filter()->unique()->toArray();
        return Murid::whereIn('id', $ids)->get()->keyBy('id');
    }

    /**
     * Halaman utama: laporan pemasukan dengan tab Tersedia / Dihapus.
     */
    public function index(Request $request)
    {
        $biayas  = BiayaMurid::with('account')->where('is_active', true)->orderBy('name')->get();
        $sekolah = ProfileSekolah::first();

        $range    = $request->get('range', '1bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');
        $tab      = $request->get('tab', 'tersedia');   // tab aktif

        // Sesuaikan range jika custom
        if ($dateFrom && $dateTo) {
            $range = 'custom';
        }

        // ── Query tab Tersedia ─────────────────────────────────────────
        $queryTersedia = $this->buildRangeQuery($request, 'tersedia');
        $pemasukans    = $queryTersedia->get();

        // ── Query tab Dihapus ──────────────────────────────────────────
        $queryDihapus  = $this->buildRangeQuery($request, 'dihapus');
        $dihapusList   = $queryDihapus->get();

        // ── Summary (hanya dari tab Tersedia) ──────────────────────────
        $totalNominal   = $pemasukans->sum('total');
        $totalAktifitas = $pemasukans->count();

        // ── Data murid cross-database ──────────────────────────────────
        $allCollection = $pemasukans->merge($dihapusList);
        $murids        = $this->attachMurid($allCollection);

        return view('admin.keuangan.pemasukan', compact(
            'biayas', 'sekolah',
            'pemasukans', 'dihapusList',
            'totalNominal', 'totalAktifitas',
            'murids',
            'range', 'dateFrom', 'dateTo', 'tab'
        ));
    }

    /**
     * Simpan data pemasukan baru (status default: tersedia).
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pemasukan' => 'required|string',
            'nominal'         => 'required|integer|min:1',
            'qty'             => 'required|integer|min:1',
            'total'           => 'required|integer|min:1',
        ]);

        if ($request->jenis_pemasukan === 'biaya_ppdb') {
            $request->validate([
                'jenis_biaya_ppdb' => 'required|string',
                'id_murid'         => 'required|integer|exists:murid,id',
            ]);
        } elseif ($request->jenis_pemasukan === 'lainnya') {
            $request->validate([
                'keterangan_lainnya' => 'required|string|max:255',
            ]);
        }

        $total = (int) $request->nominal * (int) $request->qty;

        Pemasukan::create([
            'id_murid'           => $request->jenis_pemasukan === 'biaya_ppdb' ? $request->id_murid : null,
            'jenis_pemasukan'    => $request->jenis_pemasukan,
            'jenis_biaya_ppdb'   => $request->jenis_pemasukan === 'biaya_ppdb' ? $request->jenis_biaya_ppdb : null,
            'keterangan_lainnya' => $request->jenis_pemasukan === 'lainnya'    ? $request->keterangan_lainnya : null,
            'keterangan_biaya'   => $request->keterangan_biaya ?? null,
            'nominal'            => (int) $request->nominal,
            'qty'                => (int) $request->qty,
            'total'              => $total,
            'status'             => 'tersedia',
        ]);

        return redirect()->route('keuangan.pemasukan.index')
            ->with('success', 'Data pemasukan berhasil disimpan.');
    }

    /**
     * Soft-delete: ubah status menjadi 'dihapus' (tidak dihapus dari DB).
     */
    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->update(['status' => 'dihapus']);

        return redirect()->route('keuangan.pemasukan.index', ['tab' => 'tersedia'])
            ->with('success', 'Data pemasukan dipindahkan ke tab Dihapus.');
    }

    /**
     * Pulihkan data dari tab Dihapus kembali ke Tersedia.
     */
    public function restore($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->update(['status' => 'tersedia']);

        return redirect()->route('keuangan.pemasukan.index', ['tab' => 'dihapus'])
            ->with('success', 'Data pemasukan berhasil dipulihkan ke tab Tersedia.');
    }

    /**
     * Export Excel — hanya data berstatus 'tersedia'.
     */
    public function exportExcel(Request $request)
    {
        $range    = $request->get('range', '1bulan');
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = Pemasukan::where('status', 'tersedia')->latest();

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

        $pemasukans = $query->get();
        $murids     = $this->attachMurid($pemasukans);

        $jenisBiayaMap = [
            'biaya_ppdb'     => 'Biaya PPDB',
            'donasi'         => 'Donasi',
            'bantuan_sosial' => 'Bantuan Sosial',
            'lainnya'        => 'Lainnya',
        ];

        $rows = $pemasukans->map(function ($p, $index) use ($murids, $jenisBiayaMap) {
            $murid  = $p->id_murid && isset($murids[$p->id_murid]) ? $murids[$p->id_murid] : null;
            $detail = match ($p->jenis_pemasukan) {
                'biaya_ppdb' => $p->jenis_biaya_ppdb ?? '-',
                'lainnya'    => $p->keterangan_lainnya ?? '-',
                default      => '-',
            };

            return [
                'No'           => $index + 1,
                'Tanggal'      => Carbon::parse($p->created_at)->format('d/m/Y'),
                'Jenis Biaya'  => $jenisBiayaMap[$p->jenis_pemasukan] ?? $p->jenis_pemasukan,
                'Detail'       => $detail,
                'Nama Murid'   => $murid ? $murid->nama_lengkap : '-',
                'NIS'          => $murid ? ($murid->nis_baru ?? '-') : '-',
                'Keterangan'   => $p->keterangan_biaya ?? '-',
                'Nominal (Rp)' => (int) $p->nominal,
                'QTY'          => (int) $p->qty,
                'Total (Rp)'   => (int) $p->total,
            ];
        });

        $filename = 'laporan_pemasukan_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        return (new FastExcel($rows))->download($filename);
    }

    /**
     * AJAX: Ambil detail biaya PPDB (nominal & info akun pembayaran).
     */
    public function getBiayaDetail(Request $request)
    {
        $name  = $request->get('name');
        $biaya = BiayaMurid::with('account')->where('name', $name)->where('is_active', true)->first();

        if (! $biaya) {
            return response()->json(['found' => false]);
        }

        $result = [
            'found'   => true,
            'amount'  => (int) $biaya->amount,
            'account' => null,
        ];

        if ($biaya->account) {
            $acc = $biaya->account;
            $result['account'] = [
                'bank_name'      => $acc->bank_name,
                'is_qris'        => (bool) $acc->is_qris,
                'qris_image'     => $acc->qris_image ? asset($acc->qris_image) : null,
                'account_number' => $acc->account_number,
                'account_holder' => $acc->account_holder,
            ];
        }

        return response()->json($result);
    }

    /**
     * AJAX: Cari murid aktif untuk modal pilih murid.
     */
    public function searchMurid(Request $request)
    {
        $keyword = trim($request->get('q', ''));

        $query = Murid::whereNotIn('status', ['nonaktif', 'lulus'])
            ->select('id', 'nis_baru', 'nisn', 'nama_lengkap');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('nis_baru', 'like', "%{$keyword}%")
                  ->orWhere('nisn', 'like', "%{$keyword}%")
                  ->orWhere('nama_lengkap', 'like', "%{$keyword}%");
            });
        }

        $murids = $query->limit(10)->get();
        $total  = Murid::whereNotIn('status', ['nonaktif', 'lulus'])->count();

        return response()->json(['data' => $murids, 'total' => $total]);
    }
}
