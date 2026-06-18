<?php

/**
 * Daftar modul sistem beserta aksi yang tersedia.
 * Digunakan sebagai referensi di halaman manajemen role & permission.
 *
 * Struktur:
 *   'kode_modul' => [
 *       'label'  => 'Nama tampilan modul',
 *       'icon'   => 'bootstrap-icon class',
 *       'group'  => 'Nama grup (untuk pengelompokan di UI)',
 *       'aksi'   => ['view', 'create', 'edit', 'delete'],  // aksi yang tersedia
 *   ]
 */
return [

    // ── Informasi ──────────────────────────────────────────────
    'profile_sekolah' => [
        'label' => 'Profile Sekolah',
        'icon'  => 'bi-building',
        'group' => 'Informasi',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'kelola_informasi' => [
        'label' => 'Kelola Informasi (Kegiatan, Program, Studi, dll)',
        'icon'  => 'bi-info-circle',
        'group' => 'Informasi',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'prestasi' => [
        'label' => 'Prestasi Sekolah',
        'icon'  => 'bi-trophy',
        'group' => 'Informasi',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],

    // ── PPDB ───────────────────────────────────────────────────
    'notifikasi_ppdb' => [
        'label' => 'Notifikasi & Konfirmasi PPDB',
        'icon'  => 'bi-bell',
        'group' => 'PPDB',
        'aksi'  => ['view', 'edit'],
    ],
    'ppdb_form' => [
        'label' => 'Input Data Murid Baru (PPDB)',
        'icon'  => 'bi-person-plus',
        'group' => 'PPDB',
        'aksi'  => ['view', 'create'],
    ],

    // ── Data Master ────────────────────────────────────────────
    'catatan' => [
        'label' => 'Catatan',
        'icon'  => 'bi-journal-text',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_guru' => [
        'label' => 'Data Guru',
        'icon'  => 'bi-person-badge',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'jadwal_mengajar' => [
        'label' => 'Jadwal Mengajar',
        'icon'  => 'bi-calendar3-week',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_mapel' => [
        'label' => 'Mata Pelajaran',
        'icon'  => 'bi-book',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_staff' => [
        'label' => 'Data Staff',
        'icon'  => 'bi-person-workspace',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_murid' => [
        'label' => 'Data Murid',
        'icon'  => 'bi-people',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_kelas' => [
        'label' => 'Kelola Kelas',
        'icon'  => 'bi-door-open',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_kelulusan' => [
        'label' => 'Data Kelulusan',
        'icon'  => 'bi-mortarboard',
        'group' => 'Data Master',
        'aksi'  => ['view', 'edit'],
    ],
    'data_alumni' => [
        'label' => 'Data Alumni',
        'icon'  => 'bi-people-fill',
        'group' => 'Data Master',
        'aksi'  => ['view'],
    ],
    'data_ortu' => [
        'label' => 'Data Orang Tua Murid',
        'icon'  => 'bi-person-hearts',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'data_wali' => [
        'label' => 'Data Wali Murid',
        'icon'  => 'bi-person-hearts',
        'group' => 'Data Master',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],

    // ── Dokumen ─────────────────────────────────────────────────
    'dokumen' => [
        'label' => 'Manajemen Dokumen',
        'icon'  => 'bi-folder2',
        'group' => 'Dokumen',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],

    // ── Keuangan ────────────────────────────────────────────────
    'biaya_murid' => [
        'label' => 'Biaya Murid',
        'icon'  => 'bi-cash-stack',
        'group' => 'Keuangan',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'keuangan_pemasukan' => [
        'label' => 'Pemasukan',
        'icon'  => 'bi-arrow-down-circle',
        'group' => 'Keuangan',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'keuangan_pengeluaran' => [
        'label' => 'Pengeluaran',
        'icon'  => 'bi-arrow-up-circle',
        'group' => 'Keuangan',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'laporan_keuangan' => [
        'label' => 'Laporan Keuangan',
        'icon'  => 'bi-bar-chart-line',
        'group' => 'Keuangan',
        'aksi'  => ['view'],
    ],

    // ── Pengaturan ───────────────────────────────────────────────
    'pengaturan_ppdb' => [
        'label' => 'Pengaturan Form PPDB',
        'icon'  => 'bi-gear',
        'group' => 'Pengaturan',
        'aksi'  => ['view', 'edit'],
    ],
    'manajemen_role' => [
        'label' => 'Manajemen Role & Hak Akses',
        'icon'  => 'bi-shield-lock',
        'group' => 'Pengaturan',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
    'manajemen_akun' => [
        'label' => 'Manajemen Akun User',
        'icon'  => 'bi-person-gear',
        'group' => 'Pengaturan',
        'aksi'  => ['view', 'create', 'edit', 'delete'],
    ],
];
