<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PPDBController;
use App\Http\Controllers\Informasi\InformasiController;
use App\Http\Controllers\Informasi\ProfileSekolahController;
use App\Http\Controllers\Informasi\DetailProgramController;
use App\Http\Controllers\Informasi\DetailStudiController;
use App\Http\Controllers\Informasi\DetailPrestasiController;
use App\Http\Controllers\DataMaster\KelasController;
use App\Http\Controllers\DataMaster\OrtuMuridController;
use App\Http\Controllers\DataMaster\WaliMuridController;
use App\Http\Controllers\DataMaster\MuridController;
use App\Http\Controllers\DataMaster\KelulusanController;
use App\Http\Controllers\DataMaster\AlumniController;
use App\Http\Controllers\DataMaster\StaffController;
use App\Http\Controllers\DataMaster\MapelController;
use App\Http\Controllers\DataMaster\GuruController;
use App\Http\Controllers\Keuangan\AkunPembayaranController;
use App\Http\Controllers\Keuangan\BiayaMuridController;
use App\Http\Controllers\Keuangan\PemasukanController;
use App\Http\Controllers\Keuangan\PengeluaranController;
use App\Http\Controllers\Keuangan\LaporanKeuanganController;
use App\Http\Controllers\AdminPPDBController;
use App\Http\Controllers\Dokumen\DokumenController;
use App\Http\Controllers\DataMaster\JadwalMengajarController;
use App\Http\Controllers\DataMaster\CatatanController;
use App\Http\Controllers\Pengaturan\PengaturanFormPpdbController;
use App\Http\Controllers\Pengaturan\ManajemenRoleController;
use App\Http\Controllers\AkunGuruController;
use App\Http\Controllers\AkunOrtuController;
use App\Http\Controllers\AkunRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;

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
Route::get('/dokumen/view/{uuid}', [DokumenController::class, 'viewFile'])->name('dokumen.view')->middleware('signed');

// =========================================================
// ROUTES: GUEST
// =========================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// =========================================================
// ROUTES: AUTH (Semua yang sudah login)
// =========================================================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── User Dinamis: dashboard ──────────────────────────────
    Route::middleware('role:dynamic')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan.index');
            Route::post('/catatan', [CatatanController::class, 'store'])->name('catatan.store');
            Route::get('/catatan/{uuid}/edit-data', [CatatanController::class, 'getEditData'])->name('catatan.edit-data');
            Route::put('/catatan/{uuid}', [CatatanController::class, 'update'])->name('catatan.update');
            Route::delete('/catatan/{uuid}', [CatatanController::class, 'destroy'])->name('catatan.destroy');
        });
    });

    // ── Admin-only: dashboard & pengaturan sistem ────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', [DashboardController::class, 'index'])->name('admin.home');

        // Catatan admin
        Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan.index');
        Route::post('/catatan', [CatatanController::class, 'store'])->name('catatan.store');
        Route::get('/catatan/{uuid}/edit-data', [CatatanController::class, 'getEditData'])->name('catatan.edit-data');
        Route::put('/catatan/{uuid}', [CatatanController::class, 'update'])->name('catatan.update');
        Route::delete('/catatan/{uuid}', [CatatanController::class, 'destroy'])->name('catatan.destroy');
        Route::get('/catatan/user/{id_user}', [CatatanController::class, 'showByUser'])->name('catatan.by-user');

        // Manajemen Akun (admin only)
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
        Route::get('/akun-role', [AkunRoleController::class, 'index'])->name('akun-role.index');
        Route::post('/akun-role', [AkunRoleController::class, 'store'])->name('akun-role.store');
        Route::put('/akun-role/{id}', [AkunRoleController::class, 'update'])->name('akun-role.update');
        Route::delete('/akun-role/{id}', [AkunRoleController::class, 'destroy'])->name('akun-role.destroy');
        Route::get('/akun-role/search', [AkunRoleController::class, 'search'])->name('akun-role.search');
        Route::get('/akun-role/check-username', [AkunRoleController::class, 'checkUsername'])->name('akun-role.check-username');
        Route::get('/akun-pembayaran', [AkunPembayaranController::class, 'index'])->name('akun-pembayaran.index');
        Route::post('/akun-pembayaran', [AkunPembayaranController::class, 'store'])->name('akun-pembayaran.store');
        Route::put('/akun-pembayaran/{id}', [AkunPembayaranController::class, 'update'])->name('akun-pembayaran.update');
        Route::delete('/akun-pembayaran/{id}', [AkunPembayaranController::class, 'destroy'])->name('akun-pembayaran.destroy');
        Route::get('/akun-pembayaran/check-number', [AkunPembayaranController::class, 'checkNumber'])->name('akun-pembayaran.checkNumber');

        // Pengaturan (admin only)
        Route::get('/admin/pengaturan-form-ppdb', [PengaturanFormPpdbController::class, 'index'])->name('admin.pengaturan-form-ppdb');
        Route::post('/admin/pengaturan-form-ppdb', [PengaturanFormPpdbController::class, 'update'])->name('admin.pengaturan-form-ppdb.update');
        Route::get('/admin/manajemen-role', [ManajemenRoleController::class, 'index'])->name('admin.manajemen-role.index');
        Route::post('/admin/manajemen-role', [ManajemenRoleController::class, 'store'])->name('admin.manajemen-role.store');
        Route::get('/admin/manajemen-role/{uuid}/data', [ManajemenRoleController::class, 'getRole'])->name('admin.manajemen-role.get');
        Route::put('/admin/manajemen-role/{uuid}', [ManajemenRoleController::class, 'update'])->name('admin.manajemen-role.update');
        Route::delete('/admin/manajemen-role/{uuid}', [ManajemenRoleController::class, 'destroy'])->name('admin.manajemen-role.destroy');
        Route::get('/admin/manajemen-role/{uuid}/permissions', [ManajemenRoleController::class, 'editPermissions'])->name('admin.manajemen-role.permissions');
        Route::post('/admin/manajemen-role/{uuid}/permissions', [ManajemenRoleController::class, 'savePermissions'])->name('admin.manajemen-role.permissions.save');
        Route::get('/admin/manajemen-role/{uuid}/summary', [ManajemenRoleController::class, 'getPermissionSummary'])->name('admin.manajemen-role.summary');
    });

    // ── SHARED: Admin + User dinamis (dengan permission check) ─
    // Middleware 'permission:modul' → admin selalu lolos, user dicek di DB

    // Profile Sekolah
    Route::get('/profile-sekolah', [ProfileSekolahController::class, 'index'])->name('profile-sekolah.index')->middleware('permission:profile_sekolah');
    Route::post('/profile-sekolah', [ProfileSekolahController::class, 'store'])->name('profile-sekolah.store')->middleware('permission:profile_sekolah,create');
    Route::put('/profile-sekolah/{profile_sekolah}', [ProfileSekolahController::class, 'update'])->name('profile-sekolah.update')->middleware('permission:profile_sekolah,edit');
    Route::delete('/profile-sekolah/{profile_sekolah}', [ProfileSekolahController::class, 'destroy'])->name('profile-sekolah.destroy')->middleware('permission:profile_sekolah,delete');
    Route::delete('/profile-sekolah/delete-image/{uuid}', [ProfileSekolahController::class, 'deleteImage'])->name('profile-sekolah.delete-image')->middleware('permission:profile_sekolah,edit');

    // Kelola Informasi
    Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi.index')->middleware('permission:kelola_informasi');
    Route::post('/informasi/kegiatan', [InformasiController::class, 'storeKegiatan'])->name('kegiatan.store')->middleware('permission:kelola_informasi,create');
    Route::put('/informasi/kegiatan/{id}', [InformasiController::class, 'updateKegiatan'])->name('kegiatan.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/kegiatan/{id}', [InformasiController::class, 'destroyKegiatan'])->name('kegiatan.destroy')->middleware('permission:kelola_informasi,delete');
    Route::post('/informasi/program', [InformasiController::class, 'storeProgram'])->name('program.store')->middleware('permission:kelola_informasi,create');
    Route::put('/informasi/program/{id}', [InformasiController::class, 'updateProgram'])->name('program.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/program/{id}', [InformasiController::class, 'destroyProgram'])->name('program.destroy')->middleware('permission:kelola_informasi,delete');
    Route::get('/informasi/program/{id}/detail', [DetailProgramController::class, 'show'])->name('program.detail')->middleware('permission:kelola_informasi');
    Route::post('/informasi/program/{id}/pembina', [DetailProgramController::class, 'storePembina'])->name('program.pembina.store')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/program/{id}/pembina/{pembinaId}', [DetailProgramController::class, 'destroyPembina'])->name('program.pembina.destroy')->middleware('permission:kelola_informasi,edit');
    Route::post('/informasi/program/{id}/anggota', [DetailProgramController::class, 'storeAnggota'])->name('program.anggota.store')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/program/{id}/anggota/{anggotaId}', [DetailProgramController::class, 'destroyAnggota'])->name('program.anggota.destroy')->middleware('permission:kelola_informasi,edit');
    Route::post('/informasi/program/{id}/bagan', [DetailProgramController::class, 'storeBagan'])->name('program.bagan.store')->middleware('permission:kelola_informasi,edit');
    Route::get('/informasi/program/{id}/bagan/{baganId}', [DetailProgramController::class, 'getBagan'])->name('program.bagan.get')->middleware('permission:kelola_informasi');
    Route::put('/informasi/program/{id}/bagan/{baganId}', [DetailProgramController::class, 'updateBagan'])->name('program.bagan.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/program/{id}/bagan/{baganId}', [DetailProgramController::class, 'destroyBagan'])->name('program.bagan.destroy')->middleware('permission:kelola_informasi,edit');
    Route::post('/informasi/program/{id}/catatan', [DetailProgramController::class, 'storeCatatan'])->name('program.catatan.store')->middleware('permission:kelola_informasi,edit');
    Route::get('/informasi/program/{id}/catatan/{catatanId}', [DetailProgramController::class, 'getCatatan'])->name('program.catatan.get')->middleware('permission:kelola_informasi');
    Route::put('/informasi/program/{id}/catatan/{catatanId}', [DetailProgramController::class, 'updateCatatan'])->name('program.catatan.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/program/{id}/catatan/{catatanId}', [DetailProgramController::class, 'destroyCatatan'])->name('program.catatan.destroy')->middleware('permission:kelola_informasi,edit');
    Route::get('/informasi/program/pemegang-by-tipe', [DetailProgramController::class, 'getPemegangByTipe'])->name('program.pemegang-by-tipe')->middleware('permission:kelola_informasi');
    Route::post('/informasi/prestasi', [InformasiController::class, 'storePrestasi'])->name('prestasi.store')->middleware('permission:prestasi,create');
    Route::put('/informasi/prestasi/{id}', [InformasiController::class, 'updatePrestasi'])->name('prestasi.update')->middleware('permission:prestasi,edit');
    Route::delete('/informasi/prestasi/{id}', [InformasiController::class, 'destroyPrestasi'])->name('prestasi.destroy')->middleware('permission:prestasi,delete');
    Route::get('/informasi/prestasi/{id}/detail', [DetailPrestasiController::class, 'show'])->name('prestasi.detail')->middleware('permission:prestasi');
    Route::post('/informasi/prestasi/{id}/detail', [DetailPrestasiController::class, 'updateDetail'])->name('prestasi.detail.update')->middleware('permission:prestasi,edit');
    Route::post('/informasi/prestasi/{id}/murid', [DetailPrestasiController::class, 'storeMurid'])->name('prestasi.murid.store')->middleware('permission:prestasi,edit');
    Route::delete('/informasi/prestasi/{id}/murid/{muridId}', [DetailPrestasiController::class, 'destroyMurid'])->name('prestasi.murid.destroy')->middleware('permission:prestasi,edit');
    Route::post('/informasi/prestasi/{id}/catatan', [DetailPrestasiController::class, 'storeCatatan'])->name('prestasi.catatan.store')->middleware('permission:prestasi,edit');
    Route::get('/informasi/prestasi/{id}/catatan/{catatanId}', [DetailPrestasiController::class, 'getCatatan'])->name('prestasi.catatan.get')->middleware('permission:prestasi');
    Route::put('/informasi/prestasi/{id}/catatan/{catatanId}', [DetailPrestasiController::class, 'updateCatatan'])->name('prestasi.catatan.update')->middleware('permission:prestasi,edit');
    Route::delete('/informasi/prestasi/{id}/catatan/{catatanId}', [DetailPrestasiController::class, 'destroyCatatan'])->name('prestasi.catatan.destroy')->middleware('permission:prestasi,edit');
    Route::get('/informasi/prestasi/search-murid', [DetailPrestasiController::class, 'searchMurid'])->name('prestasi.search-murid')->middleware('permission:prestasi');
    Route::post('/informasi/artikel', [InformasiController::class, 'storeArtikel'])->name('artikel.store')->middleware('permission:kelola_informasi,create');
    Route::put('/informasi/artikel/{id}', [InformasiController::class, 'updateArtikel'])->name('artikel.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/artikel/{id}', [InformasiController::class, 'destroyArtikel'])->name('artikel.destroy')->middleware('permission:kelola_informasi,delete');
    Route::delete('/informasi/artikel/foto/{id}', [InformasiController::class, 'destroyFotoArtikel'])->name('artikel.foto.destroy')->middleware('permission:kelola_informasi,edit');
    Route::post('/informasi/studi', [InformasiController::class, 'storeStudi'])->name('studi.store')->middleware('permission:kelola_informasi,create');
    Route::put('/informasi/studi/{id}', [InformasiController::class, 'updateStudi'])->name('studi.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/studi/{id}', [InformasiController::class, 'destroyStudi'])->name('studi.destroy')->middleware('permission:kelola_informasi,delete');
    Route::get('/informasi/studi/{id}/detail', [DetailStudiController::class, 'show'])->name('studi.detail')->middleware('permission:kelola_informasi');
    Route::post('/informasi/studi/{id}/kepala', [DetailStudiController::class, 'storeKepala'])->name('studi.kepala.store')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/studi/{id}/kepala/{kepalaId}', [DetailStudiController::class, 'destroyKepala'])->name('studi.kepala.destroy')->middleware('permission:kelola_informasi,edit');
    Route::post('/informasi/studi/{id}/kelas', [DetailStudiController::class, 'storeKelas'])->name('studi.kelas.store')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/studi/{id}/kelas/{kelasId}', [DetailStudiController::class, 'destroyKelas'])->name('studi.kelas.destroy')->middleware('permission:kelola_informasi,edit');
    Route::post('/informasi/studi/{id}/catatan', [DetailStudiController::class, 'storeCatatan'])->name('studi.catatan.store')->middleware('permission:kelola_informasi,edit');
    Route::get('/informasi/studi/{id}/catatan/{catatanId}', [DetailStudiController::class, 'getCatatan'])->name('studi.catatan.get')->middleware('permission:kelola_informasi');
    Route::put('/informasi/studi/{id}/catatan/{catatanId}', [DetailStudiController::class, 'updateCatatan'])->name('studi.catatan.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/studi/{id}/catatan/{catatanId}', [DetailStudiController::class, 'destroyCatatan'])->name('studi.catatan.destroy')->middleware('permission:kelola_informasi,edit');
    Route::get('/informasi/studi/sumber-by-tipe', [DetailStudiController::class, 'getSumberByTipe'])->name('studi.sumber-by-tipe')->middleware('permission:kelola_informasi');
    Route::post('/informasi/info-sekolah', [InformasiController::class, 'storeOrUpdateInfoSekolah'])->name('info.sekolah.save')->middleware('permission:profile_sekolah,edit');
    Route::post('/informasi/brosur', [InformasiController::class, 'storeBrosur'])->name('brosur.store')->middleware('permission:kelola_informasi,create');
    Route::put('/informasi/brosur/{id}', [InformasiController::class, 'updateBrosur'])->name('brosur.update')->middleware('permission:kelola_informasi,edit');
    Route::delete('/informasi/brosur/{id}', [InformasiController::class, 'destroyBrosur'])->name('brosur.destroy')->middleware('permission:kelola_informasi,delete');

    // PPDB
    Route::prefix('admin/ppdb-notifications')->group(function () {
        Route::get('/', [AdminPPDBController::class, 'index'])->name('admin.ppdb.index')->middleware('permission:notifikasi_ppdb');
        Route::get('/data', [AdminPPDBController::class, 'getNotifications'])->name('admin.ppdb.data')->middleware('permission:notifikasi_ppdb');
        Route::get('/count', [AdminPPDBController::class, 'getBadgeCount'])->name('admin.ppdb.count');
        Route::get('/detail/{id}', [AdminPPDBController::class, 'getDetail'])->name('admin.ppdb.detail')->middleware('permission:notifikasi_ppdb');
        Route::post('/confirm/{id}', [AdminPPDBController::class, 'confirm'])->name('admin.ppdb.confirm')->middleware('permission:notifikasi_ppdb,edit');
        Route::post('/reject/{id}', [AdminPPDBController::class, 'reject'])->name('admin.ppdb.reject')->middleware('permission:notifikasi_ppdb,edit');
        Route::get('/bukti/{id}', [AdminPPDBController::class, 'getBuktiPembayaran'])->name('admin.ppdb.bukti')->middleware('permission:notifikasi_ppdb');
        Route::get('/bukti-file/{id}', [AdminPPDBController::class, 'serveBuktiPembayaran'])->name('admin.ppdb.bukti.serve')->middleware('permission:notifikasi_ppdb');
        Route::get('/dokumen', [AdminPPDBController::class, 'serveDokumen'])->name('admin.ppdb.dokumen')->middleware('permission:notifikasi_ppdb');
        Route::get('/cash-biayas', [AdminPPDBController::class, 'getCashBiayas'])->name('admin.ppdb.cashBiayas')->middleware('permission:notifikasi_ppdb');
    });
    Route::post('/admin/ppdb/toggle', [AdminPPDBController::class, 'toggleStatus'])->name('admin.ppdb.toggle')->middleware('permission:notifikasi_ppdb,edit');
    Route::get('/admin/ppdb/status', [AdminPPDBController::class, 'getStatus'])->name('admin.ppdb.status');

    // PPDB Form (input murid baru)
    Route::get('/murid/search', [MuridController::class, 'search'])->name('murid.search')->middleware('permission:data_murid');
    Route::get('/get-murid-by-kelas', [MuridController::class, 'getMuridByKelas'])->name('murid.getByKelas')->middleware('permission:data_murid');
    Route::get('/murid/check-nisn', [MuridController::class, 'checkNISN'])->name('murid.check-nisn');
    Route::get('/murid/check-nik', [MuridController::class, 'checkNIK'])->name('murid.check-nik');
    Route::post('/murid/auto-save', [MuridController::class, 'autoSaveDraft'])->name('murid.auto-save');
    Route::get('/murid/get-draft', [MuridController::class, 'getDraftData'])->name('murid.get-draft');
    Route::get('/murid/dokumen', [MuridController::class, 'serveDokumen'])->name('murid.dokumen')->middleware('permission:data_murid');
    Route::get('/murid/{uuid}/detail', [MuridController::class, 'detail'])->name('murid.detail')->middleware('permission:data_murid');
    Route::get('/murid/{uuid}/pdf', [MuridController::class, 'downloadPdf'])->name('murid.pdf')->middleware('permission:data_murid');
    Route::post('/murid/{uuid}/restore', [MuridController::class, 'restore'])->name('murid.restore')->middleware('permission:data_murid,edit');
    Route::get('/murid/{uuid}/download-surat', [MuridController::class, 'downloadSurat'])->name('murid.download-surat')->middleware('permission:data_murid');
    Route::get('/murid/{uuid}/edit', [MuridController::class, 'edit'])->name('murid.edit')->middleware('permission:data_murid,edit');
    Route::put('/murid/{uuid}', [MuridController::class, 'update'])->name('murid.update')->middleware('permission:data_murid,edit');
    Route::delete('/murid/{uuid}', [MuridController::class, 'destroy'])->name('murid.destroy')->middleware('permission:data_murid,delete');
    Route::get('/murid', [MuridController::class, 'index'])->name('murid.index')->middleware('permission:data_murid');
    Route::get('/murid/create', [MuridController::class, 'create'])->name('murid.create')->middleware('permission:ppdb_form,create');
    Route::post('/murid', [MuridController::class, 'store'])->name('murid.store')->middleware('permission:ppdb_form,create');

    // Data Guru
    Route::get('/guru/search', [GuruController::class, 'search'])->name('guru.search')->middleware('permission:data_guru');
    Route::post('/guru/{id}/restore', [GuruController::class, 'restore'])->name('guru.restore')->middleware('permission:data_guru,edit');
    Route::get('/guru/{id}/download-surat', [GuruController::class, 'downloadSurat'])->name('guru.download-surat')->middleware('permission:data_guru');
    Route::get('/guru', [GuruController::class, 'index'])->name('guru.index')->middleware('permission:data_guru');
    Route::post('/guru', [GuruController::class, 'store'])->name('guru.store')->middleware('permission:data_guru,create');
    Route::get('/guru/{guru}', [GuruController::class, 'show'])->name('guru.show')->middleware('permission:data_guru');
    Route::get('/guru/{guru}/edit', [GuruController::class, 'edit'])->name('guru.edit')->middleware('permission:data_guru,edit');
    Route::put('/guru/{guru}', [GuruController::class, 'update'])->name('guru.update')->middleware('permission:data_guru,edit');
    Route::patch('/guru/{guru}', [GuruController::class, 'update'])->middleware('permission:data_guru,edit');
    Route::delete('/guru/{guru}', [GuruController::class, 'destroy'])->name('guru.destroy')->middleware('permission:data_guru,delete');

    // Jadwal Mengajar — route statis harus di atas route wildcard {id}
    Route::get('/jadwal-mengajar', [JadwalMengajarController::class, 'index'])->name('jadwal-mengajar.index')->middleware('permission:jadwal_mengajar');
    Route::post('/jadwal-mengajar', [JadwalMengajarController::class, 'store'])->name('jadwal-mengajar.store')->middleware('permission:jadwal_mengajar,create');
    Route::get('/jadwal-mengajar/poster', [JadwalMengajarController::class, 'showPoster'])->name('jadwal-mengajar.poster')->middleware('permission:jadwal_mengajar');
    Route::get('/jadwal-mengajar/guru/{id_guru}/mapel', [JadwalMengajarController::class, 'getMapelByGuru'])->name('jadwal-mengajar.mapel-by-guru')->middleware('permission:jadwal_mengajar');
    Route::get('/jadwal-mengajar/guru/kelas', [JadwalMengajarController::class, 'getKelasByGuruMapel'])->name('jadwal-mengajar.kelas-by-guru-mapel')->middleware('permission:jadwal_mengajar');
    Route::get('/jadwal-mengajar/{id}', [JadwalMengajarController::class, 'show'])->name('jadwal-mengajar.show')->middleware('permission:jadwal_mengajar');
    Route::put('/jadwal-mengajar/{id}', [JadwalMengajarController::class, 'update'])->name('jadwal-mengajar.update')->middleware('permission:jadwal_mengajar,edit');
    Route::delete('/jadwal-mengajar/{id}', [JadwalMengajarController::class, 'destroy'])->name('jadwal-mengajar.destroy')->middleware('permission:jadwal_mengajar,delete');

    // Mata Pelajaran
    Route::get('/mapel/search', [MapelController::class, 'search'])->name('mapel.search')->middleware('permission:data_mapel');
    Route::get('/mapel', [MapelController::class, 'index'])->name('mapel.index')->middleware('permission:data_mapel');
    Route::post('/mapel', [MapelController::class, 'store'])->name('mapel.store')->middleware('permission:data_mapel,create');
    Route::get('/mapel/{mapel}', [MapelController::class, 'show'])->name('mapel.show')->middleware('permission:data_mapel');
    Route::get('/mapel/{mapel}/edit', [MapelController::class, 'edit'])->name('mapel.edit')->middleware('permission:data_mapel,edit');
    Route::put('/mapel/{mapel}', [MapelController::class, 'update'])->name('mapel.update')->middleware('permission:data_mapel,edit');
    Route::patch('/mapel/{mapel}', [MapelController::class, 'update'])->middleware('permission:data_mapel,edit');
    Route::delete('/mapel/{mapel}', [MapelController::class, 'destroy'])->name('mapel.destroy')->middleware('permission:data_mapel,delete');

    // Data Staff
    Route::get('staff/search', [StaffController::class, 'search'])->name('staff.search')->middleware('permission:data_staff');
    Route::post('staff/{id}/restore', [StaffController::class, 'restore'])->name('staff.restore')->middleware('permission:data_staff,edit');
    Route::get('staff/{id}/download-surat', [StaffController::class, 'downloadSurat'])->name('staff.download-surat')->middleware('permission:data_staff');
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index')->middleware('permission:data_staff');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store')->middleware('permission:data_staff,create');
    Route::get('/staff/{staff}', [StaffController::class, 'show'])->name('staff.show')->middleware('permission:data_staff');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit')->middleware('permission:data_staff,edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update')->middleware('permission:data_staff,edit');
    Route::patch('/staff/{staff}', [StaffController::class, 'update'])->middleware('permission:data_staff,edit');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy')->middleware('permission:data_staff,delete');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index')->middleware('permission:data_kelas');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store')->middleware('permission:data_kelas,create');
    Route::get('/kelas/{kelas}', [KelasController::class, 'show'])->name('kelas.show')->middleware('permission:data_kelas');
    Route::get('/kelas/{kelas}/edit', [KelasController::class, 'edit'])->name('kelas.edit')->middleware('permission:data_kelas,edit');
    Route::put('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update')->middleware('permission:data_kelas,edit');
    Route::patch('/kelas/{kelas}', [KelasController::class, 'update'])->middleware('permission:data_kelas,edit');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy')->middleware('permission:data_kelas,delete');
    Route::post('/kelas/tambah-murid', [KelasController::class, 'addStudent'])->name('kelas.addStudent')->middleware('permission:data_kelas,edit');
    Route::delete('/kelas/hapus-murid/{id_murid}', [KelasController::class, 'removeStudent'])->name('kelas.removeStudent')->middleware('permission:data_kelas,edit');
    Route::post('/kelas/{id}/wali-kelas', [KelasController::class, 'setWaliKelas'])->name('kelas.setWaliKelas')->middleware('permission:data_kelas,edit');
    Route::delete('/kelas/{id}/wali-kelas', [KelasController::class, 'removeWaliKelas'])->name('kelas.removeWaliKelas')->middleware('permission:data_kelas,edit');

    // Ortu & Wali Murid
    Route::get('/ortu-murid/search', [OrtuMuridController::class, 'search'])->name('ortu-murid.search')->middleware('permission:data_ortu');
    Route::get('/ortu-murid', [OrtuMuridController::class, 'index'])->name('ortu-murid.index')->middleware('permission:data_ortu');
    Route::get('/wali-murid/search', [WaliMuridController::class, 'search'])->name('wali-murid.search')->middleware('permission:data_wali');
    Route::get('/wali-murid', [WaliMuridController::class, 'index'])->name('wali-murid.index')->middleware('permission:data_wali');

    // Kelulusan
    Route::get('/data-kelulusan', [KelulusanController::class, 'index'])->name('kelulusan.index')->middleware('permission:data_kelulusan');
    Route::get('/data-kelulusan/search', [KelulusanController::class, 'search'])->name('kelulusan.search')->middleware('permission:data_kelulusan');
    Route::get('/data-kelulusan/{uuid}/edit', [KelulusanController::class, 'edit'])->name('kelulusan.edit')->middleware('permission:data_kelulusan,edit');
    Route::post('/data-kelulusan/{uuid}/update', [KelulusanController::class, 'update'])->name('kelulusan.update')->middleware('permission:data_kelulusan,edit');
    Route::get('/data-kelulusan/berkas/ijazah/{uuid}', [KelulusanController::class, 'viewIjazah'])->name('kelulusan.view.ijazah')->middleware('permission:data_kelulusan');
    Route::get('/data-kelulusan/berkas/raport/{uuid}', [KelulusanController::class, 'viewRaport'])->name('kelulusan.view.raport')->middleware('permission:data_kelulusan');
    Route::get('/data-kelulusan/surat-kelulusan/{uuid}', [KelulusanController::class, 'viewSuratKelulusan'])->name('kelulusan.view.surat')->middleware('permission:data_kelulusan');

    // Alumni
    Route::get('/data-alumni', [AlumniController::class, 'index'])->name('alumni.index')->middleware('permission:data_alumni');
    Route::get('/data-alumni/search', [AlumniController::class, 'search'])->name('alumni.search')->middleware('permission:data_alumni');

    // Keuangan
    Route::get('/biaya-murid', [BiayaMuridController::class, 'index'])->name('biaya-murid.index')->middleware('permission:biaya_murid');
    Route::post('/biaya-murid', [BiayaMuridController::class, 'store'])->name('biaya-murid.store')->middleware('permission:biaya_murid,create');
    Route::post('/biaya-murid/check-name', [BiayaMuridController::class, 'checkFeeName'])->name('biaya-murid.check-name');
    Route::put('/biaya-murid/{id}', [BiayaMuridController::class, 'update'])->name('biaya-murid.update')->middleware('permission:biaya_murid,edit');
    Route::delete('/biaya-murid/{id}', [BiayaMuridController::class, 'destroy'])->name('biaya-murid.destroy')->middleware('permission:biaya_murid,delete');
    Route::prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('/laporan', [LaporanKeuanganController::class, 'index'])->name('laporan.index')->middleware('permission:laporan_keuangan');
        Route::get('/laporan/export-excel', [LaporanKeuanganController::class, 'exportExcel'])->name('laporan.export-excel')->middleware('permission:laporan_keuangan');
        Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index')->middleware('permission:keuangan_pemasukan');
        Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store')->middleware('permission:keuangan_pemasukan,create');
        Route::get('/pemasukan/{id}/edit-data', [PemasukanController::class, 'getEditData'])->name('pemasukan.edit-data')->middleware('permission:keuangan_pemasukan');
        Route::post('/pemasukan/{id}/update', [PemasukanController::class, 'update'])->name('pemasukan.update')->middleware('permission:keuangan_pemasukan,edit');
        Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy')->middleware('permission:keuangan_pemasukan,delete');
        Route::post('/pemasukan/{id}/restore', [PemasukanController::class, 'restore'])->name('pemasukan.restore')->middleware('permission:keuangan_pemasukan,edit');
        Route::get('/pemasukan/search-murid', [PemasukanController::class, 'searchMurid'])->name('pemasukan.search-murid')->middleware('permission:keuangan_pemasukan');
        Route::get('/pemasukan/export-excel', [PemasukanController::class, 'exportExcel'])->name('pemasukan.export-excel')->middleware('permission:keuangan_pemasukan');
        Route::get('/pemasukan/biaya-detail', [PemasukanController::class, 'getBiayaDetail'])->name('pemasukan.biaya-detail')->middleware('permission:keuangan_pemasukan');
        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index')->middleware('permission:keuangan_pengeluaran');
        Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store')->middleware('permission:keuangan_pengeluaran,create');
        Route::get('/pengeluaran/{id}/edit-data', [PengeluaranController::class, 'getEditData'])->name('pengeluaran.edit-data')->middleware('permission:keuangan_pengeluaran');
        Route::post('/pengeluaran/{id}/update', [PengeluaranController::class, 'update'])->name('pengeluaran.update')->middleware('permission:keuangan_pengeluaran,edit');
        Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy')->middleware('permission:keuangan_pengeluaran,delete');
        Route::post('/pengeluaran/{id}/restore', [PengeluaranController::class, 'restore'])->name('pengeluaran.restore')->middleware('permission:keuangan_pengeluaran,edit');
        Route::get('/pengeluaran/export-excel', [PengeluaranController::class, 'exportExcel'])->name('pengeluaran.export-excel')->middleware('permission:keuangan_pengeluaran');
        Route::get('/pengeluaran/bukti/{id}', [PengeluaranController::class, 'viewBukti'])->name('pengeluaran.bukti')->middleware('permission:keuangan_pengeluaran');
    });

    // Dokumen
    Route::get('/dokumen', [DokumenController::class, 'index'])->name('dokumen.index')->middleware('permission:dokumen');
    Route::get('/dokumen/folder/{uuid}', [DokumenController::class, 'detailFolder'])->name('dokumen.folder.detail')->middleware('permission:dokumen');
    Route::post('/dokumen/folder/store', [DokumenController::class, 'storeFolder'])->name('dokumen.folder.store')->middleware('permission:dokumen,create');
    Route::post('/dokumen/file/store', [DokumenController::class, 'storeFile'])->name('dokumen.file.store')->middleware('permission:dokumen,create');
    Route::put('/dokumen/rename/{uuid}', [DokumenController::class, 'rename'])->name('dokumen.rename')->middleware('permission:dokumen,edit');
    Route::delete('/dokumen/destroy/{uuid}', [DokumenController::class, 'destroy'])->name('dokumen.destroy')->middleware('permission:dokumen,delete');
    Route::get('/dokumen/download/{uuid}', [DokumenController::class, 'download'])->name('dokumen.download')->middleware('permission:dokumen');
    Route::get('/dokumen/search', [DokumenController::class, 'search'])->name('dokumen.search')->middleware('permission:dokumen');

}); // end auth
