<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Models\Guru;
use App\Models\Murid;
use App\Http\Controllers\ProfileSekolahController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PPDBController;


Route::get('/', [IndexController::class, 'index'])->name('home');
Route::get('/login', [AuthController::class, 'index'])->name('login');

Route::get('/ppdb', [PPDBController::class, 'index'])->name('ppdb.index');
Route::post('/ppdb', [PPDBController::class, 'store'])->name('ppdb.store');
Route::get('/ppdb/berhasil', [PPDBController::class, 'success'])->name('ppdb.success');

// Route Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Dashboard (Contoh placeholder)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard_admin', fn() => view('dashboard_admin.index'))->name('admin.home');
    Route::get('/dashboard_wali', fn() => view('dashboard_wali.index'))->name('wali.home');
    Route::get('/informasi', [App\Http\Controllers\InformasiController::class, 'index'])->name('informasi.index');

    Route::get('/dashboard_guru', fn() => view('dashboard_guru.index'))->name('guru.home');
    Route::get('/absensi_guru', fn() => view('dashboard_guru.absensi'))->name('guru.absensi');
    Route::get('/pelanggaran_guru', [App\Http\Controllers\GuruController::class, 'pelanggaran'])->name('guru.pelanggaran');
    Route::get('/keaktifan_guru', fn() => view('dashboard_guru.keaktifan'))->name('guru.keaktifan');
    Route::get('/dashboard_wali', fn() => view('dashboard_wali.index'))->name('wali.home');
    
    // Route CRUD per Tab
    Route::post('/informasi/kegiatan', [App\Http\Controllers\InformasiController::class, 'storeKegiatan'])->name('kegiatan.store');
    Route::delete('/informasi/kegiatan/{id}', [App\Http\Controllers\InformasiController::class, 'destroyKegiatan'])->name('kegiatan.destroy');
    
    Route::post('/informasi/program', [App\Http\Controllers\InformasiController::class, 'storeProgram'])->name('program.store');
    Route::delete('/informasi/program/{id}', [App\Http\Controllers\InformasiController::class, 'destroyProgram'])->name('program.destroy');
    
    Route::post('/informasi/prestasi', [App\Http\Controllers\InformasiController::class, 'storePrestasi'])->name('prestasi.store');
    Route::delete('/informasi/prestasi/{id}', [App\Http\Controllers\InformasiController::class, 'destroyPrestasi'])->name('prestasi.destroy');
    
    Route::post('/informasi/artikel', [App\Http\Controllers\InformasiController::class, 'storeArtikel'])->name('artikel.store');
    Route::delete('/informasi/artikel/{id}', [App\Http\Controllers\InformasiController::class, 'destroyArtikel'])->name('artikel.destroy');
    Route::resource('kelas', App\Http\Controllers\KelasController::class);
    Route::post('/kelas/tambah-murid', [App\Http\Controllers\KelasController::class, 'addStudent'])->name('kelas.addStudent');
    Route::delete('/kelas/hapus-murid/{id_murid}', [App\Http\Controllers\KelasController::class, 'removeStudent'])->name('kelas.removeStudent');

    Route::get('/wali-murid', [App\Http\Controllers\WaliMuridController::class, 'index'])->name('wali-murid.index');
    Route::get('/wali-murid/search', [App\Http\Controllers\WaliMuridController::class, 'search'])->name('wali-murid.search');
    Route::resource('wali-murid', App\Http\Controllers\WaliMuridController::class);

    Route::resource('guru', App\Http\Controllers\GuruController::class);
    Route::get('/guru/search', [App\Http\Controllers\GuruController::class, 'search'])->name('guru.search');

    Route::resource('murid', App\Http\Controllers\MuridController::class);
    Route::get('/murid/search', [App\Http\Controllers\MuridController::class, 'search'])->name('murid.search');

    Route::delete('/profile-sekolah/delete-image/{id}', [App\Http\Controllers\ProfileSekolahController::class, 'deleteImage'])->name('profile-sekolah.delete-image'); 

    Route::put('/informasi/kegiatan/{id}', [App\Http\Controllers\InformasiController::class, 'updateKegiatan'])->name('kegiatan.update');
    Route::put('/informasi/program/{id}', [App\Http\Controllers\InformasiController::class, 'updateProgram'])->name('program.update');
    Route::put('/informasi/prestasi/{id}', [App\Http\Controllers\InformasiController::class, 'updatePrestasi'])->name('prestasi.update');
    Route::put('/informasi/artikel/{id}', [App\Http\Controllers\InformasiController::class, 'updateArtikel'])->name('artikel.update');
    Route::delete('/informasi/prestasi/foto/{id}', [App\Http\Controllers\InformasiController::class, 'destroyFotoPrestasi'])->name('prestasi.foto.destroy');
    Route::delete('/informasi/artikel/foto/{id}', [App\Http\Controllers\InformasiController::class, 'destroyFotoArtikel'])->name('artikel.foto.destroy');

    Route::get('/akun-guru', [App\Http\Controllers\AkunGuruController::class, 'index'])->name('akun-guru.index');
    Route::get('/akun-guru/search', [App\Http\Controllers\AkunGuruController::class, 'search'])->name('akun-guru.search');
    Route::post('/akun-guru', [App\Http\Controllers\AkunGuruController::class, 'store'])->name('akun-guru.store');
    Route::put('/akun-guru/{id_user}', [App\Http\Controllers\AkunGuruController::class, 'update'])->name('akun-guru.update');
    Route::delete('/akun-guru/{id_user}', [App\Http\Controllers\AkunGuruController::class, 'destroy'])->name('akun-guru.destroy');

    Route::get('/akun-wali', [App\Http\Controllers\AkunWaliController::class, 'index'])->name('akun-wali.index');
    Route::get('/akun-wali/search', [App\Http\Controllers\AkunWaliController::class, 'search'])->name('akun-wali.search');
    Route::post('/akun-wali', [App\Http\Controllers\AkunWaliController::class, 'store'])->name('akun-wali.store');
    Route::put('/akun-wali/{id_user}', [App\Http\Controllers\AkunWaliController::class, 'update'])->name('akun-wali.update');
    Route::delete('/akun-wali/{id_user}', [App\Http\Controllers\AkunWaliController::class, 'destroy'])->name('akun-wali.destroy');

    Route::get('/pelanggaran', [App\Http\Controllers\PelanggaranController::class, 'index'])->name('pelanggaran.index');
    Route::post('/pelanggaran/aturan', [App\Http\Controllers\PelanggaranController::class, 'storeAturan'])->name('pelanggaran.storeAturan');
    Route::post('/pelanggaran/murid', [App\Http\Controllers\PelanggaranController::class, 'storePelanggaranMurid'])->name('pelanggaran.storeMurid');
    Route::delete('/pelanggaran/{id}', [App\Http\Controllers\PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');
    Route::put('/pelanggaran/aturan/{id}', [App\Http\Controllers\PelanggaranController::class, 'updateAturan'])->name('pelanggaran.updateAturan');
    Route::delete('/pelanggaran/aturan/{id}', [App\Http\Controllers\PelanggaranController::class, 'destroyAturan'])->name('pelanggaran.destroyAturan');

    Route::get('/guru/pelanggaran/search', [App\Http\Controllers\GuruController::class, 'searchPelanggaran'])->name('guru.pelanggaran.search');

    Route::get('/get-murid-by-kelas', [App\Http\Controllers\MuridController::class, 'getMuridByKelas'])->name('murid.getByKelas'); 

    Route::resource('konfirmasi-pelanggaran', App\Http\Controllers\KonfirmasiPelanggaranController::class);
    Route::get('/admin/konfirmasi-pelanggaran', [App\Http\Controllers\KonfirmasiPelanggaranController::class, 'index'])->name('admin.pelanggaran.index');
    Route::post('/admin/konfirmasi-pelanggaran/{id}/approve', [App\Http\Controllers\KonfirmasiPelanggaranController::class, 'approve'])->name('admin.pelanggaran.approve');
    Route::post('/admin/konfirmasi-pelanggaran/{id}/reject', [App\Http\Controllers\KonfirmasiPelanggaranController::class, 'reject'])->name('admin.pelanggaran.reject');

    Route::get('/keaktifan_guru', [App\Http\Controllers\KeaktifanController::class, 'index'])->name('guru.keaktifan');
    Route::post('/keaktifan_guru', [App\Http\Controllers\KeaktifanController::class, 'store'])->name('guru.keaktifan.store');
    Route::get('/keaktifan_guru/{id}/edit', [App\Http\Controllers\KeaktifanController::class, 'edit'])->name('guru.keaktifan.edit');
    Route::put('/keaktifan_guru/{id}', [App\Http\Controllers\KeaktifanController::class, 'update'])->name('guru.keaktifan.update');

    Route::get('/admin/pelanggaran/count', [App\Http\Controllers\KonfirmasiPelanggaranController::class, 'getPendingCount'])->name('admin.pelanggaran.count');

    Route::get('/absensi_guru', [AbsensiController::class, 'index'])->name('guru.absensi');
    Route::post('/absensi_guru', [AbsensiController::class, 'store'])->name('guru.absensi.store');
    Route::get('/absensi/arsip', [AbsensiController::class, 'getArsip'])->name('guru.absensi.arsip');
    Route::get('/absensi/rekap-individu', [AbsensiController::class, 'getRekapMurid'])->name('guru.absensi.rekap');
    
    Route::resource('keaktifan-admin', App\Http\Controllers\KeaktifanAdminController::class);
    Route::get('/admin/keaktifan-murid', [App\Http\Controllers\KeaktifanAdminController::class, 'index'])->name('admin.keaktifan.index');

    Route::get('/pelanggaran/ajax-search', [App\Http\Controllers\PelanggaranController::class, 'ajaxSearch'])->name('pelanggaran.ajaxSearch');

    Route::get('/admin/arsip-absen', [App\Http\Controllers\AdminAbsensiController::class, 'index'])->name('admin.arsip.index');
    Route::get('/admin/arsip-absen/murid', [App\Http\Controllers\AdminAbsensiController::class, 'getMurid'])->name('admin.arsip.murid');
    Route::get('/admin/arsip-absen/rekap', [App\Http\Controllers\AdminAbsensiController::class, 'getRekapIndividu'])->name('admin.arsip.rekap');

    Route::get('/admin/aktifitas-guru', [App\Http\Controllers\AdminAktifitasGuruController::class, 'index'])->name('admin.aktifitas.index');
    Route::get('/admin/aktifitas-guru/data', [App\Http\Controllers\AdminAktifitasGuruController::class, 'getChartData'])->name('admin.aktifitas.data');

    // Route Khusus Admin PPDB
Route::prefix('admin/ppdb-notifications')->group(function () {
    Route::get('/', [App\Http\Controllers\AdminPPDBController::class, 'index'])->name('admin.ppdb.index');
    Route::get('/data', [App\Http\Controllers\AdminPPDBController::class, 'getNotifications'])->name('admin.ppdb.data');
    Route::get('/count', [App\Http\Controllers\AdminPPDBController::class, 'getBadgeCount'])->name('admin.ppdb.count');
    Route::get('/detail/{id}', [App\Http\Controllers\AdminPPDBController::class, 'getDetail'])->name('admin.ppdb.detail');
    Route::post('/confirm/{id}', [App\Http\Controllers\AdminPPDBController::class, 'confirm'])->name('admin.ppdb.confirm');
});

    Route::post('/admin/ppdb/toggle', [App\Http\Controllers\AdminPPDBController::class, 'toggleStatus'])->name('admin.ppdb.toggle');
    Route::get('/admin/ppdb/status', [App\Http\Controllers\AdminPPDBController::class, 'getStatus'])->name('admin.ppdb.status');

    Route::get('/informasi', [App\Http\Controllers\InformasiController::class, 'index'])->name('informasi.index');
    Route::resource('profile-sekolah', ProfileSekolahController::class);
});