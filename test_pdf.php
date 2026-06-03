<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Murid;
use App\Models\BiayaMurid;
use Illuminate\Support\Facades\DB;

try {
    // Ambil murid pertama dari DB untuk test
    $murid = Murid::with(['ortu', 'wali', 'dokumen'])->first();

    if (!$murid) {
        echo "Tidak ada data murid di database. Membuat data dummy...\n";
        // Test dengan data dummy tanpa DB
        $murid = new Murid([
            'id' => 1,
            'nama_lengkap' => 'TEST MURID',
            'jenis_kelamin' => 'laki-laki',
            'nisn' => '1234567890',
            'status' => 'konfirmasi',
        ]);
    }

    $biayas = BiayaMurid::with('account')->orderBy('id')->get();
    $sekolah = DB::table('profile_sekolah')->first();

    $dokumenDiupload = [];
    $labelMap = [
        'pasfoto' => 'Pasfoto', 'ktp_ayah' => 'KTP Ayah', 'ktp_ibu' => 'KTP Ibu',
        'ktp_wali' => 'KTP Wali', 'kartu_keluarga' => 'Kartu Keluarga',
        'akte_kelahiran' => 'Akte Kelahiran', 'ijazah_terakhir' => 'Ijazah Terakhir',
        'transkip_nilai' => 'Transkip Nilai', 'surat_kelulusan' => 'Surat Kelulusan',
        'surat_keterangan_hasil_ujian' => 'Surat Keterangan Hasil Ujian',
        'surat_pindahan' => 'Surat Pindahan', 'formulir_fisik' => 'Formulir Fisik',
    ];
    foreach ($labelMap as $field => $label) {
        $dokumenDiupload[$label] = $murid->dokumen && !empty($murid->dokumen->$field);
    }

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('format_file.pdf', [
        'murid'           => $murid,
        'sekolah'         => $sekolah,
        'biayas'          => $biayas,
        'dokumenDiupload' => $dokumenDiupload,
        'pasfotoPath'     => null,
        'logoPath'        => null,
    ])->setPaper('a4', 'portrait');

    $output = $pdf->output();
    file_put_contents('test_output.pdf', $output);
    echo "PDF berhasil dibuat: " . strlen($output) . " bytes\n";
    echo "Disimpan ke: test_output.pdf\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
