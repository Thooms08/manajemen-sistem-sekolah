<?php

use Illuminate\Support\Facades\Route;
use App\Models\Guru;
use App\Models\Murid;

// =========================================================
// CONTROLLER: GUEST & PUBLIC
// =========================================================
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PPDBController;

// =========================================================
// CONTROLLER: ROLE ADMIN
// =========================================================

//Use: informasi_sekolah
use App\Http\Controllers\{InformasiController, ProfileSekolahController};

//Use: Data Master
use App\Http\Controllers\{KelasController, OrtuMuridController, WaliMuridController, MuridController, 
KelulusanController, AlumniController, StaffController};

//Use: Keuangan
use App\Http\Controllers\{AkunPembayaranController, BiayaMuridController};

//Use: Index
use App\Http\Controllers\AdminPPDBController;

//Use: Pengaturan
use App\Http\Controllers\PengaturanFormPpdbController;

use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\KonfirmasiPelanggaranController;
use App\Http\Controllers\KeaktifanAdminController;
use App\Http\Controllers\AdminAbsensiController;
use App\Http\Controllers\AdminAktifitasGuruController;
use App\Http\Controllers\AkunGuruController;
use App\Http\Controllers\AkunOrtuController;


// =========================================================
// CONTROLLER: ROLE GURU
// =========================================================
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KeaktifanController;

// =========================================================
// CONTROLLER: SHARED (Digunakan di lebih dari 1 role)
// =========================================================
use App\Http\Controllers\GuruController;


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
    
    // Route Logout bersifat global untuk semua role yang sudah login
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // -----------------------------------------------------
    // ROLE: ADMIN
    // -----------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', fn() => view('admin.index'))->name('admin.home');
        
        // Informasi Sekolah
        // ========== INFORMASI ==========
        Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi.index');

        // Kegiatan
        Route::post('/informasi/kegiatan', [InformasiController::class, 'storeKegiatan'])->name('kegiatan.store');
        Route::put('/informasi/kegiatan/{id}', [InformasiController::class, 'updateKegiatan'])->name('kegiatan.update');
        Route::delete('/informasi/kegiatan/{id}', [InformasiController::class, 'destroyKegiatan'])->name('kegiatan.destroy');

        // Program Sekolah
        Route::post('/informasi/program', [InformasiController::class, 'storeProgram'])->name('program.store');
        Route::put('/informasi/program/{id}', [InformasiController::class, 'updateProgram'])->name('program.update');
        Route::delete('/informasi/program/{id}', [InformasiController::class, 'destroyProgram'])->name('program.destroy');

        // Prestasi
        Route::post('/informasi/prestasi', [InformasiController::class, 'storePrestasi'])->name('prestasi.store');
        Route::put('/informasi/prestasi/{id}', [InformasiController::class, 'updatePrestasi'])->name('prestasi.update');
        Route::delete('/informasi/prestasi/{id}', [InformasiController::class, 'destroyPrestasi'])->name('prestasi.destroy');
        //Route::delete('/informasi/prestasi/foto/{id}', [InformasiController::class, 'destroyFotoPrestasi'])->name('prestasi.foto.destroy');

        // Artikel
        Route::post('/informasi/artikel', [InformasiController::class, 'storeArtikel'])->name('artikel.store');
        Route::put('/informasi/artikel/{id}', [InformasiController::class, 'updateArtikel'])->name('artikel.update');
        Route::delete('/informasi/artikel/{id}', [InformasiController::class, 'destroyArtikel'])->name('artikel.destroy');
        Route::delete('/informasi/artikel/foto/{id}', [InformasiController::class, 'destroyFotoArtikel'])->name('artikel.foto.destroy');

        // Program Studi (BARU)
        Route::post('/informasi/studi', [InformasiController::class, 'storeStudi'])->name('studi.store');
        Route::put('/informasi/studi/{id}', [InformasiController::class, 'updateStudi'])->name('studi.update');
        Route::delete('/informasi/studi/{id}', [InformasiController::class, 'destroyStudi'])->name('studi.destroy');

        // Info Sekolah (BARU)
        Route::post('/informasi/info-sekolah', [InformasiController::class, 'storeOrUpdateInfoSekolah'])->name('info.sekolah.save');

        // Master Data: Kelas
        Route::resource('kelas', KelasController::class);
        Route::post('/kelas/tambah-murid', [KelasController::class, 'addStudent'])->name('kelas.addStudent');
        Route::delete('/kelas/hapus-murid/{id_murid}', [KelasController::class, 'removeStudent'])->name('kelas.removeStudent');

        // Master Data: Ortu Murid
        Route::get('/ortu-murid/search', [OrtuMuridController::class, 'search'])->name('ortu-murid.search');
        Route::resource('ortu-murid', OrtuMuridController::class);

        // Master Data: Wali Murid
        Route::get('/wali-murid/search', [WaliMuridController::class, 'search'])->name('wali-murid.search');
        Route::resource('wali-murid', WaliMuridController::class);

        // Master Data: Guru
        Route::get('/guru/search', [GuruController::class, 'search'])->name('guru.search');
        Route::resource('guru', GuruController::class);

        // Master Data: Murid
        Route::get('/murid/search', [MuridController::class, 'search'])->name('murid.search');
        Route::get('/get-murid-by-kelas', [MuridController::class, 'getMuridByKelas'])->name('murid.getByKelas');
        Route::get('/murid/check-nisn', [MuridController::class, 'checkNISN'])->name('murid.check-nisn');
        Route::get('/murid/check-nik', [MuridController::class, 'checkNIK'])->name('murid.check-nik');
        Route::post('/murid/auto-save', [MuridController::class, 'autoSaveDraft'])->name('murid.auto-save');
        Route::get('/murid/get-draft', [MuridController::class, 'getDraftData'])->name('murid.get-draft');
        Route::get('/murid/dokumen', [MuridController::class, 'serveDokumen'])->name('murid.dokumen');
        Route::get('/murid/{id}/detail', [MuridController::class, 'detail'])->name('murid.detail');
        Route::get('/murid/{id}/pdf', [MuridController::class, 'downloadPdf'])->name('murid.pdf');
        Route::resource('murid', MuridController::class);

        //Master Data: Kelulusan
        Route::get('/data-kelulusan', [KelulusanController::class, 'index'])->name('kelulusan.index');
        Route::get('/data-kelulusan/search', [KelulusanController::class, 'search'])->name('kelulusan.search');
        Route::get('/data-kelulusan/{uuid}/edit', [KelulusanController::class, 'edit'])->name('kelulusan.edit');
        Route::post('/data-kelulusan/{uuid}/update', [KelulusanController::class, 'update'])->name('kelulusan.update');
        Route::get('/data-kelulusan/berkas/ijazah/{uuid}', [KelulusanController::class, 'viewIjazah'])->name('kelulusan.view.ijazah');
        Route::get('/data-kelulusan/berkas/raport/{uuid}', [KelulusanController::class, 'viewRaport'])->name('kelulusan.view.raport');
        Route::get('/data-kelulusan/surat-kelulusan/{uuid}', [KelulusanController::class, 'viewSuratKelulusan'])->name('kelulusan.view.surat');

        //Master Data: Alumni
        Route::get('/data-alumni', [AlumniController::class, 'index'])->name('alumni.index');
        Route::get('/data-alumni/search', [AlumniController::class, 'search'])->name('alumni.search');

        // Profile Sekolah
        Route::resource('profile-sekolah', ProfileSekolahController::class);
        Route::delete('/profile-sekolah/delete-image/{id}', [ProfileSekolahController::class, 'deleteImage'])->name('profile-sekolah.delete-image'); 

        // Manajemen Akun
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

        // Akun Pembayaran (Bank / QRIS)
        Route::get('/akun-pembayaran', [AkunPembayaranController::class, 'index'])->name('akun-pembayaran.index');
        Route::post('/akun-pembayaran', [AkunPembayaranController::class, 'store'])->name('akun-pembayaran.store');
        Route::put('/akun-pembayaran/{id}', [AkunPembayaranController::class, 'update'])->name('akun-pembayaran.update');
        Route::delete('/akun-pembayaran/{id}', [AkunPembayaranController::class, 'destroy'])->name('akun-pembayaran.destroy');
        Route::get('/akun-pembayaran/check-number', [AkunPembayaranController::class, 'checkNumber'])->name('akun-pembayaran.checkNumber');

        // Biaya Murid
        Route::get('/biaya-murid', [BiayaMuridController::class, 'index'])->name('biaya-murid.index');
        Route::post('/biaya-murid', [BiayaMuridController::class, 'store'])->name('biaya-murid.store');
        Route::post('/biaya-murid/check-name', [BiayaMuridController::class, 'checkFeeName'])->name('biaya-murid.check-name');
        Route::put('/biaya-murid/{id}', [BiayaMuridController::class, 'update'])->name('biaya-murid.update');
        Route::delete('/biaya-murid/{id}', [BiayaMuridController::class, 'destroy'])->name('biaya-murid.destroy');

        // Manajemen Pelanggaran
        Route::get('/pelanggaran', [PelanggaranController::class, 'index'])->name('pelanggaran.index');
        Route::get('/pelanggaran/ajax-search', [PelanggaranController::class, 'ajaxSearch'])->name('pelanggaran.ajaxSearch');
        Route::post('/pelanggaran/aturan', [PelanggaranController::class, 'storeAturan'])->name('pelanggaran.storeAturan');
        Route::put('/pelanggaran/aturan/{id}', [PelanggaranController::class, 'updateAturan'])->name('pelanggaran.updateAturan');
        Route::delete('/pelanggaran/aturan/{id}', [PelanggaranController::class, 'destroyAturan'])->name('pelanggaran.destroyAturan');
        Route::post('/pelanggaran/murid', [PelanggaranController::class, 'storePelanggaranMurid'])->name('pelanggaran.storeMurid');
        Route::delete('/pelanggaran/{id}', [PelanggaranController::class, 'destroy'])->name('pelanggaran.destroy');

        Route::get('staff/search', [StaffController::class, 'search'])->name('staff.search');
        Route::resource('staff', StaffController::class);

        // Konfirmasi Pelanggaran
        Route::resource('konfirmasi-pelanggaran', KonfirmasiPelanggaranController::class);
        Route::get('/admin/konfirmasi-pelanggaran', [KonfirmasiPelanggaranController::class, 'index'])->name('admin.pelanggaran.index');
        Route::get('/admin/pelanggaran/count', [KonfirmasiPelanggaranController::class, 'getPendingCount'])->name('admin.pelanggaran.count');
        Route::post('/admin/konfirmasi-pelanggaran/{id}/approve', [KonfirmasiPelanggaranController::class, 'approve'])->name('admin.pelanggaran.approve');
        Route::post('/admin/konfirmasi-pelanggaran/{id}/reject', [KonfirmasiPelanggaranController::class, 'reject'])->name('admin.pelanggaran.reject');

        // Monitoring Admin (Absen & Aktifitas)
        Route::resource('keaktifan-admin', KeaktifanAdminController::class);
        Route::get('/admin/keaktifan-murid', [KeaktifanAdminController::class, 'index'])->name('admin.keaktifan.index');
        
        Route::get('/admin/arsip-absen', [AdminAbsensiController::class, 'index'])->name('admin.arsip.index');
        Route::get('/admin/arsip-absen/murid', [AdminAbsensiController::class, 'getMurid'])->name('admin.arsip.murid');
        Route::get('/admin/arsip-absen/rekap', [AdminAbsensiController::class, 'getRekapIndividu'])->name('admin.arsip.rekap');

        Route::get('/admin/aktifitas-guru', [AdminAktifitasGuruController::class, 'index'])->name('admin.aktifitas.index');
        Route::get('/admin/aktifitas-guru/data', [AdminAktifitasGuruController::class, 'getChartData'])->name('admin.aktifitas.data');

        // Admin PPDB Notifications
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

        Route::get('/admin/pengaturan-form-ppdb', [PengaturanFormPpdbController::class, 'index'])->name('admin.pengaturan-form-ppdb');
        Route::post('/admin/pengaturan-form-ppdb', [PengaturanFormPpdbController::class, 'update'])->name('admin.pengaturan-form-ppdb.update');
    });

    // -----------------------------------------------------
    // ROLE: GURU
    // -----------------------------------------------------
    Route::middleware('role:guru')->group(function () {
        Route::get('/dashboard_guru', fn() => view('dashboard_guru.index'))->name('guru.home');
        
        // Absensi Guru
        Route::get('/absensi_guru', [AbsensiController::class, 'index'])->name('guru.absensi');
        Route::post('/absensi_guru', [AbsensiController::class, 'store'])->name('guru.absensi.store');
        Route::get('/absensi/arsip', [AbsensiController::class, 'getArsip'])->name('guru.absensi.arsip');
        Route::get('/absensi/rekap-individu', [AbsensiController::class, 'getRekapMurid'])->name('guru.absensi.rekap');
        
        // Pelanggaran (Akses Guru)
        Route::get('/pelanggaran_guru', [GuruController::class, 'pelanggaran'])->name('guru.pelanggaran');
        Route::get('/guru/pelanggaran/search', [GuruController::class, 'searchPelanggaran'])->name('guru.pelanggaran.search');
        
        // Keaktifan Guru
        Route::get('/keaktifan_guru', [KeaktifanController::class, 'index'])->name('guru.keaktifan');
        Route::post('/keaktifan_guru', [KeaktifanController::class, 'store'])->name('guru.keaktifan.store');
        Route::get('/keaktifan_guru/{id}/edit', [KeaktifanController::class, 'edit'])->name('guru.keaktifan.edit');
        Route::put('/keaktifan_guru/{id}', [KeaktifanController::class, 'update'])->name('guru.keaktifan.update');
    });

    // -----------------------------------------------------
    // ROLE: ORTU MURID (DIPERSIAPKAN / DIKOMENTARI)
    // -----------------------------------------------------
    /*
    Route::middleware('role:ortu')->group(function () {
        Route::get('/dashboard_ortu', fn() => view('dashboard_ortu.index'))->name('ortu.home');
        // Tambahkan route khusus ortu murid lainnya di sini nantinya
    });
    */
    
});