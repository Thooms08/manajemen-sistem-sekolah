<?php

use Illuminate\Support\Facades\Route;

// =========================================================
// CONTROLLER: GUEST & PUBLIC
// =========================================================
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PPDBController;

// =========================================================
// CONTROLLER: ROLE ADMIN
// =========================================================

// Informasi & Profil Sekolah
use App\Http\Controllers\Informasi\InformasiController;
use App\Http\Controllers\Informasi\ProfileSekolahController;

// Data Master
use App\Http\Controllers\DataMaster\KelasController;
use App\Http\Controllers\DataMaster\OrtuMuridController;
use App\Http\Controllers\DataMaster\WaliMuridController;
use App\Http\Controllers\DataMaster\MuridController;
use App\Http\Controllers\DataMaster\KelulusanController;
use App\Http\Controllers\DataMaster\AlumniController;
use App\Http\Controllers\DataMaster\StaffController;
use App\Http\Controllers\DataMaster\MapelController;
use App\Http\Controllers\DataMaster\GuruController;

// Keuangan
use App\Http\Controllers\Keuangan\AkunPembayaranController;
use App\Http\Controllers\Keuangan\BiayaMuridController;
use App\Http\Controllers\Keuangan\PemasukanController;
use App\Http\Controllers\Keuangan\PengeluaranController;
use App\Http\Controllers\Keuangan\LaporanKeuanganController;

// PPDB Admin
use App\Http\Controllers\AdminPPDBController;

// Dokumen
use App\Http\Controllers\Dokumen\DokumenController;

// Pengaturan
use App\Http\Controllers\Pengaturan\PengaturanFormPpdbController;

// Akun
use App\Http\Controllers\AkunGuruController;
use App\Http\Controllers\AkunOrtuController;
use App\Http\Controllers\DashboardController;


// =========================================================
// ROUTES: PUBLIC
// =========================================================
Route::get('/', [IndexController::class, 'index'])->name('home');

Route::get('/ppdb', [PPDBController::class, 'index'])->name('ppdb.index');
Route::post('/ppdb', [PPDBController::class, 'store'])->name('ppdb.store');
Route::get('/ppdb/berhasil', [PPDBController::class, 'success'])->name('ppdb.success');
Route::get('/ppdb/check-nisn', [PPDBController::class, 'checkNISN'])->name('ppdb.check-nisn');
Route::get('/ppdb/check-nik', [PPDBController::class, 'checkNIK'])->name('ppdb.check-nik');
Route::post('/ppdb/auto-save', [PPDBController::class, 'autoSaveDraft'])->name('ppdb.auto-save');
Route::get('/ppdb/get-draft', [PPDBController::class, 'getDraftData'])->name('ppdb.get-draft');

Route::get('/artikel/{slug}', [InformasiController::class, 'showArtikel'])->name('artikel.show');
Route::get('/brosur', [IndexController::class, 'brosur'])->name('brosur.publik');

// Dokumen View via signed URL (aman tanpa session cookie)
Route::get('/dokumen/view/{uuid}', [DokumenController::class, 'viewFile'])
    ->name('dokumen.view')
    ->middleware('signed');


// =========================================================
// ROUTES: GUEST (Belum Login)
// =========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});


// =========================================================
// ROUTES: AUTH (Sudah Login)
// =========================================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ---------------------------------------------------------
    // ROLE: ADMIN
    // ---------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [DashboardController::class, 'index'])->name('admin.home');

        // ── Informasi Sekolah ──────────────────────────────────
        Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi.index');

        Route::post('/informasi/kegiatan', [InformasiController::class, 'storeKegiatan'])->name('kegiatan.store');
        Route::put('/informasi/kegiatan/{id}', [InformasiController::class, 'updateKegiatan'])->name('kegiatan.update');
        Route::delete('/informasi/kegiatan/{id}', [InformasiController::class, 'destroyKegiatan'])->name('kegiatan.destroy');

        Route::post('/informasi/program', [InformasiController::class, 'storeProgram'])->name('program.store');
        Route::put('/informasi/program/{id}', [InformasiController::class, 'updateProgram'])->name('program.update');
        Route::delete('/informasi/program/{id}', [InformasiController::class, 'destroyProgram'])->name('program.destroy');

        Route::post('/informasi/prestasi', [InformasiController::class, 'storePrestasi'])->name('prestasi.store');
        Route::put('/informasi/prestasi/{id}', [InformasiController::class, 'updatePrestasi'])->name('prestasi.update');
        Route::delete('/informasi/prestasi/{id}', [InformasiController::class, 'destroyPrestasi'])->name('prestasi.destroy');

        Route::post('/informasi/artikel', [InformasiController::class, 'storeArtikel'])->name('artikel.store');
        Route::put('/informasi/artikel/{id}', [InformasiController::class, 'updateArtikel'])->name('artikel.update');
        Route::delete('/informasi/artikel/{id}', [InformasiController::class, 'destroyArtikel'])->name('artikel.destroy');
        Route::delete('/informasi/artikel/foto/{id}', [InformasiController::class, 'destroyFotoArtikel'])->name('artikel.foto.destroy');

        Route::post('/informasi/studi', [InformasiController::class, 'storeStudi'])->name('studi.store');
        Route::put('/informasi/studi/{id}', [InformasiController::class, 'updateStudi'])->name('studi.update');
        Route::delete('/informasi/studi/{id}', [InformasiController::class, 'destroyStudi'])->name('studi.destroy');

        Route::post('/informasi/info-sekolah', [InformasiController::class, 'storeOrUpdateInfoSekolah'])->name('info.sekolah.save');

        // ── Brosur ─────────────────────────────────────────────
        Route::post('/informasi/brosur', [InformasiController::class, 'storeBrosur'])->name('brosur.store');
        Route::put('/informasi/brosur/{id}', [InformasiController::class, 'updateBrosur'])->name('brosur.update');
        Route::delete('/informasi/brosur/{id}', [InformasiController::class, 'destroyBrosur'])->name('brosur.destroy');

        // ── Profile Sekolah ────────────────────────────────────
        Route::resource('profile-sekolah', ProfileSekolahController::class);
        Route::delete('/profile-sekolah/delete-image/{id}', [ProfileSekolahController::class, 'deleteImage'])->name('profile-sekolah.delete-image');

        // ── Kelas ──────────────────────────────────────────────
        Route::resource('kelas', KelasController::class);
        Route::post('/kelas/tambah-murid', [KelasController::class, 'addStudent'])->name('kelas.addStudent');
        Route::delete('/kelas/hapus-murid/{id_murid}', [KelasController::class, 'removeStudent'])->name('kelas.removeStudent');
        Route::post('/kelas/{id}/wali-kelas', [KelasController::class, 'setWaliKelas'])->name('kelas.setWaliKelas');
        Route::delete('/kelas/{id}/wali-kelas', [KelasController::class, 'removeWaliKelas'])->name('kelas.removeWaliKelas');

        // ── Ortu Murid ─────────────────────────────────────────
        Route::get('/ortu-murid/search', [OrtuMuridController::class, 'search'])->name('ortu-murid.search');
        Route::resource('ortu-murid', OrtuMuridController::class);

        // ── Wali Murid ─────────────────────────────────────────
        Route::get('/wali-murid/search', [WaliMuridController::class, 'search'])->name('wali-murid.search');
        Route::resource('wali-murid', WaliMuridController::class);

        // ── Guru ───────────────────────────────────────────────
        Route::get('/guru/search', [GuruController::class, 'search'])->name('guru.search');
        Route::post('/guru/{id}/restore', [GuruController::class, 'restore'])->name('guru.restore');
        Route::get('/guru/{id}/download-surat', [GuruController::class, 'downloadSurat'])->name('guru.download-surat');
        Route::resource('guru', GuruController::class);

        // ── Mapel ──────────────────────────────────────────────
        Route::get('/mapel/search', [MapelController::class, 'search'])->name('mapel.search');
        Route::resource('mapel', MapelController::class);

        // ── Murid ──────────────────────────────────────────────
        Route::get('/murid/search', [MuridController::class, 'search'])->name('murid.search');
        Route::get('/get-murid-by-kelas', [MuridController::class, 'getMuridByKelas'])->name('murid.getByKelas');
        Route::get('/murid/check-nisn', [MuridController::class, 'checkNISN'])->name('murid.check-nisn');
        Route::get('/murid/check-nik', [MuridController::class, 'checkNIK'])->name('murid.check-nik');
        Route::post('/murid/auto-save', [MuridController::class, 'autoSaveDraft'])->name('murid.auto-save');
        Route::get('/murid/get-draft', [MuridController::class, 'getDraftData'])->name('murid.get-draft');
        Route::get('/murid/dokumen', [MuridController::class, 'serveDokumen'])->name('murid.dokumen');
        Route::get('/murid/{uuid}/detail', [MuridController::class, 'detail'])->name('murid.detail');
        Route::get('/murid/{uuid}/pdf', [MuridController::class, 'downloadPdf'])->name('murid.pdf');
        Route::post('/murid/{uuid}/restore', [MuridController::class, 'restore'])->name('murid.restore');
        Route::get('/murid/{uuid}/download-surat', [MuridController::class, 'downloadSurat'])->name('murid.download-surat');
        Route::get('/murid/{uuid}/edit', [MuridController::class, 'edit'])->name('murid.edit');
        Route::put('/murid/{uuid}', [MuridController::class, 'update'])->name('murid.update');
        Route::delete('/murid/{uuid}', [MuridController::class, 'destroy'])->name('murid.destroy');
        Route::get('/murid', [MuridController::class, 'index'])->name('murid.index');
        Route::get('/murid/create', [MuridController::class, 'create'])->name('murid.create');
        Route::post('/murid', [MuridController::class, 'store'])->name('murid.store');

        // ── Kelulusan ──────────────────────────────────────────
        Route::get('/data-kelulusan', [KelulusanController::class, 'index'])->name('kelulusan.index');
        Route::get('/data-kelulusan/search', [KelulusanController::class, 'search'])->name('kelulusan.search');
        Route::get('/data-kelulusan/{uuid}/edit', [KelulusanController::class, 'edit'])->name('kelulusan.edit');
        Route::post('/data-kelulusan/{uuid}/update', [KelulusanController::class, 'update'])->name('kelulusan.update');
        Route::get('/data-kelulusan/berkas/ijazah/{uuid}', [KelulusanController::class, 'viewIjazah'])->name('kelulusan.view.ijazah');
        Route::get('/data-kelulusan/berkas/raport/{uuid}', [KelulusanController::class, 'viewRaport'])->name('kelulusan.view.raport');
        Route::get('/data-kelulusan/surat-kelulusan/{uuid}', [KelulusanController::class, 'viewSuratKelulusan'])->name('kelulusan.view.surat');

        // ── Alumni ─────────────────────────────────────────────
        Route::get('/data-alumni', [AlumniController::class, 'index'])->name('alumni.index');
        Route::get('/data-alumni/search', [AlumniController::class, 'search'])->name('alumni.search');

        // ── Staff ──────────────────────────────────────────────
        Route::get('staff/search', [StaffController::class, 'search'])->name('staff.search');
        Route::post('staff/{id}/restore', [StaffController::class, 'restore'])->name('staff.restore');
        Route::get('staff/{id}/download-surat', [StaffController::class, 'downloadSurat'])->name('staff.download-surat');
        Route::resource('staff', StaffController::class);

        // ── Manajemen Akun ─────────────────────────────────────
        Route::get('/akun-guru', [AkunGuruController::class, 'index'])->name('akun-guru.index');
        Route::get('/akun-guru/search', [AkunGuruController::class, 'search'])->name('akun-guru.search');
        Route::post('/akun-guru', [AkunGuruController::class, 'store'])->name('akun-guru.store');
        Route::put('/akun-guru/{id_user}', [AkunGuruController::class, 'update'])->name('akun-guru.update');
        Route::delete('/akun-guru/{id_user}', [AkunGuruController::class, 'destroy'])->name('akun-guru.destroy');

        Route::get('/akun-ortu', [AkunOrtuController::class, 'index'])->name('akun-ortu.index');
        Route::get('/akun-ortu/search', [AkunOrtuController::class, 'search'])->name('akun-ortu.search');
        Route::post('/akun-ortu', [AkunOrtuController::class, 'store'])->name('akun-ortu.store');
        Route::put('/akun-ortu/{id_user}', [AkunOrtuController::class, 'update'])->name('akun-ortu.update');
        Route::delete('/akun-ortu/{id_user}', [AkunOrtuController::class, 'destroy'])->name('akun-ortu.destroy');

        // ── Akun Pembayaran ────────────────────────────────────
        Route::get('/akun-pembayaran', [AkunPembayaranController::class, 'index'])->name('akun-pembayaran.index');
        Route::post('/akun-pembayaran', [AkunPembayaranController::class, 'store'])->name('akun-pembayaran.store');
        Route::put('/akun-pembayaran/{id}', [AkunPembayaranController::class, 'update'])->name('akun-pembayaran.update');
        Route::delete('/akun-pembayaran/{id}', [AkunPembayaranController::class, 'destroy'])->name('akun-pembayaran.destroy');
        Route::get('/akun-pembayaran/check-number', [AkunPembayaranController::class, 'checkNumber'])->name('akun-pembayaran.checkNumber');

        // ── Biaya Murid ────────────────────────────────────────
        Route::get('/biaya-murid', [BiayaMuridController::class, 'index'])->name('biaya-murid.index');
        Route::post('/biaya-murid', [BiayaMuridController::class, 'store'])->name('biaya-murid.store');
        Route::post('/biaya-murid/check-name', [BiayaMuridController::class, 'checkFeeName'])->name('biaya-murid.check-name');
        Route::put('/biaya-murid/{id}', [BiayaMuridController::class, 'update'])->name('biaya-murid.update');
        Route::delete('/biaya-murid/{id}', [BiayaMuridController::class, 'destroy'])->name('biaya-murid.destroy');

        // ── Keuangan ───────────────────────────────────────────
        Route::prefix('keuangan')->name('keuangan.')->group(function () {
            // Laporan
            Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('laporan.index');
            Route::get('/laporan/export-excel', [LaporanKeuanganController::class, 'exportExcel'])->name('laporan.export-excel');

            // Pemasukan
            Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
            Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
            Route::get('/pemasukan/{id}/edit-data', [PemasukanController::class, 'getEditData'])->name('pemasukan.edit-data');
            Route::post('/pemasukan/{id}/update', [PemasukanController::class, 'update'])->name('pemasukan.update');
            Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');
            Route::post('/pemasukan/{id}/restore', [PemasukanController::class, 'restore'])->name('pemasukan.restore');
            Route::get('/pemasukan/search-murid', [PemasukanController::class, 'searchMurid'])->name('pemasukan.search-murid');
            Route::get('/pemasukan/export-excel', [PemasukanController::class, 'exportExcel'])->name('pemasukan.export-excel');
            Route::get('/pemasukan/biaya-detail', [PemasukanController::class, 'getBiayaDetail'])->name('pemasukan.biaya-detail');

            // Pengeluaran
            Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
            Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
            Route::get('/pengeluaran/{id}/edit-data', [PengeluaranController::class, 'getEditData'])->name('pengeluaran.edit-data');
            Route::post('/pengeluaran/{id}/update', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
            Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
            Route::post('/pengeluaran/{id}/restore', [PengeluaranController::class, 'restore'])->name('pengeluaran.restore');
            Route::get('/pengeluaran/export-excel', [PengeluaranController::class, 'exportExcel'])->name('pengeluaran.export-excel');
            Route::get('/pengeluaran/bukti/{id}', [PengeluaranController::class, 'viewBukti'])->name('pengeluaran.bukti');
        });

        // ── Manajemen Dokumen ──────────────────────────────────
        Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index');
        Route::get('/dokumen/folder/{uuid}', [DokumenController::class, 'detailFolder'])->name('dokumen.folder.detail');
        Route::post('/dokumen/folder/store', [DokumenController::class, 'storeFolder'])->name('dokumen.folder.store');
        Route::post('/dokumen/file/store', [DokumenController::class, 'storeFile'])->name('dokumen.file.store');
        Route::put('/dokumen/rename/{uuid}', [DokumenController::class, 'rename'])->name('dokumen.rename');
        Route::delete('/dokumen/destroy/{uuid}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
        Route::get('/dokumen/download/{uuid}', [DokumenController::class, 'download'])->name('dokumen.download');
        Route::get('/dokumen/search', [DokumenController::class, 'search'])->name('dokumen.search');

        // ── PPDB Admin ─────────────────────────────────────────
        Route::prefix('admin/ppdb-notifications')->group(function () {
            Route::get('/', [AdminPPDBController::class, 'index'])->name('admin.ppdb.index');
            Route::get('/data', [AdminPPDBController::class, 'getNotifications'])->name('admin.ppdb.data');
            Route::get('/count', [AdminPPDBController::class, 'getBadgeCount'])->name('admin.ppdb.count');
            Route::get('/detail/{id}', [AdminPPDBController::class, 'getDetail'])->name('admin.ppdb.detail');
            Route::post('/confirm/{id}', [AdminPPDBController::class, 'confirm'])->name('admin.ppdb.confirm');
            Route::get('/dokumen', [AdminPPDBController::class, 'serveDokumen'])->name('admin.ppdb.dokumen');
            Route::get('/cash-biayas', [AdminPPDBController::class, 'getCashBiayas'])->name('admin.ppdb.cashBiayas');
        });
        Route::post('/admin/ppdb/toggle', [AdminPPDBController::class, 'toggleStatus'])->name('admin.ppdb.toggle');
        Route::get('/admin/ppdb/status', [AdminPPDBController::class, 'getStatus'])->name('admin.ppdb.status');

        // ── Pengaturan Form PPDB ───────────────────────────────
        Route::get('/admin/pengaturan-form-ppdb', [PengaturanFormPpdbController::class, 'index'])->name('admin.pengaturan-form-ppdb');
        Route::post('/admin/pengaturan-form-ppdb', [PengaturanFormPpdbController::class, 'update'])->name('admin.pengaturan-form-ppdb.update');
    });

});
