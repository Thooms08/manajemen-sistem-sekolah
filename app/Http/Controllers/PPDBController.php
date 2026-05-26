<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PPDBController extends Controller
{
public function index() {
    $isOpen = DB::table('profile_sekolah')->value('is_ppdb_open');

    if (!$isOpen) {
        return view('index.ppdb_tutup'); 
    }

    return view('index.ppdb'); 
}

    public function success()
    {
        return view('index.ppdb_berhasil');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Menyesuaikan dengan field form_ppdb)
        $request->validate([
            // Data Murid
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'nisn' => 'required|numeric|digits:10|unique:murid,nisn',
            'nik' => 'required|numeric|unique:murid,nik',
            'no_hp' => 'required',
            'alamat_email' => 'required|email|unique:murid,alamat_email',
            
            // Data Wali
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 2. Simpan Data Murid (Status Otomatis Pending)
            $muridId = DB::table('murid')->insertGetId([
                'nama_lengkap'    => $request->nama_lengkap,
                'jenis_kelamin'   => $request->jenis_kelamin,
                'nisn'            => $request->nisn,
                'nik'             => $request->nik,
                'tempat_lahir'    => $request->tempat_lahir,
                'tgl_lahir'       => $request->tgl_lahir,
                'rt_rw'           => $request->rt_rw,
                'desa_kelurahan'  => $request->desa_kelurahan,
                'kota_kabupaten'  => $request->kota_kabupaten,
                'provinsi'        => $request->provinsi,
                'alamat_detail'   => $request->alamat_detail,
                'transportasi'    => $request->transportasi,
                'no_hp'           => $request->no_hp,
                'alamat_email'    => $request->alamat_email,
                'sekolah_asal'    => $request->sekolah_asal,
                'tinggi_badan'    => $request->tinggi_badan,
                'berat_badan'     => $request->berat_badan,
                'anak_ke'         => $request->anak_ke,
                'jml_saudara'     => $request->jml_saudara,
                'jumlah_kakak'    => $request->jumlah_kakak,
                'jumlah_adik'     => $request->jumlah_adik,
                'status'          => 'pending', 
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. Simpan Data Wali Murid
            DB::table('wali_murid')->insert([
                'id_murid'            => $muridId,
                'nama_ayah'           => $request->nama_ayah,
                'tempat_lahir_ayah'   => $request->tempat_lahir_ayah,
                'tgl_lahir_ayah'      => $request->tgl_lahir_ayah,
                'pendidikan_ayah'     => $request->pendidikan_ayah,
                'pekerjaan_ayah'      => $request->pekerjaan_ayah,
                'penghasilan_ayah'    => $request->penghasilan_ayah,
                'status_ayah'         => $request->status_ayah,
                'nama_ibu'            => $request->nama_ibu,
                'tempat_lahir_ibu'    => $request->tempat_lahir_ibu,
                'tgl_lahir_ibu'       => $request->tgl_lahir_ibu,
                'pendidikan_ibu'      => $request->pendidikan_ibu,
                'pekerjaan_ibu'       => $request->pekerjaan_ibu,
                'penghasilan_ibu'     => $request->penghasilan_ibu,
                'status_ibu'          => $request->status_ibu,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            DB::commit();
            return redirect()->route('ppdb.success');

        } catch (\Exception $e) {
    DB::rollback();
    // Tampilkan error aslinya agar kita tahu apa yang salah
    return dd($e->getMessage()); 
}
    }
}