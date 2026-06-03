<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Pendaftaran Peserta Didik Baru</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            background: #fff;
            padding: 15mm 15mm 15mm 20mm;
        }

        /* ── KOP SURAT ─────────────────────────────────────────────────── */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .kop-logo { display: table-cell; width: 70px; vertical-align: middle; text-align: center; }
        .kop-logo img { width: 60px; height: 60px; object-fit: contain; }
        .kop-text { display: table-cell; vertical-align: middle; text-align: center; }
        .kop-text .nama-sekolah { font-size: 15pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .kop-text .alamat-sekolah { font-size: 8.5pt; margin-top: 3px; }
        .kop-pasfoto { display: table-cell; width: 90px; vertical-align: middle; text-align: center; }
        .kop-pasfoto .box-foto {
            width: 80px; height: 100px;
            border: 1px solid #000;
            display: flex; align-items: center; justify-content: center;
            font-size: 8pt; color: #999;
        }
        .kop-pasfoto .box-foto img { width: 80px; height: 100px; object-fit: cover; }

        /* ── JUDUL ──────────────────────────────────────────────────────── */
        .judul {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 10px 0 12px 0;
            letter-spacing: 1px;
        }

        /* ── SECTION HEADER ─────────────────────────────────────────────── */
        .section-header {
            background-color: #d0e8d0;
            font-weight: bold;
            font-size: 9.5pt;
            padding: 3px 6px;
            margin: 10px 0 5px 0;
            border: 1px solid #555;
        }

        /* ── TABEL DATA ─────────────────────────────────────────────────── */
        .tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .tabel-data td {
            padding: 3px 5px;
            font-size: 9.5pt;
            vertical-align: top;
        }
        .tabel-data .lbl { width: 35%; font-weight: normal; }
        .tabel-data .sep { width: 3%; text-align: center; }
        .tabel-data .val {
            width: 62%;
            border-bottom: 1px dotted #666;
        }

        /* ── TABEL 2 KOLOM ──────────────────────────────────────────────── */
        .tabel-2col { width: 100%; border-collapse: collapse; }
        .tabel-2col td { width: 50%; vertical-align: top; padding: 0 5px 0 0; }
        .tabel-2col td:last-child { padding: 0 0 0 5px; }

        /* ── CHECKLIST ──────────────────────────────────────────────────── */
        .checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .checklist-table td {
            padding: 2px 5px;
            font-size: 9pt;
            vertical-align: middle;
        }
        .box-check {
            display: inline-block;
            width: 12px; height: 12px;
            border: 1px solid #000;
            text-align: center; line-height: 12px;
            font-size: 9pt; font-weight: bold;
            margin-right: 5px;
            vertical-align: middle;
        }
        .checked { background: #000; color: #fff; }

        /* ── TANDA TANGAN ───────────────────────────────────────────────── */
        .ttd-row {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .ttd-col {
            display: table-cell;
            width: 33%;
            text-align: center;
            font-size: 9pt;
            padding: 0 10px;
        }
        .ttd-space { height: 55px; }
        .ttd-line { border-top: 1px solid #000; padding-top: 3px; }

        /* ── NOMOR PENDAFTARAN ──────────────────────────────────────────── */
        .no-pendaftaran {
            text-align: right;
            font-size: 9pt;
            margin-bottom: 6px;
        }

        /* ── FOOTER ─────────────────────────────────────────────────────── */
        .footer-note {
            margin-top: 12px;
            font-size: 8pt;
            border-top: 1px solid #999;
            padding-top: 5px;
            color: #555;
        }
    </style>
</head>
<body>

{{-- ══ KOP SURAT ══ --}}
<div class="kop">
    <div class="kop-logo">
        @if($logoPath)
            <img src="{{ $logoPath }}">
        @else
            <div style="width:60px;height:60px;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:7pt;color:#999;">LOGO</div>
        @endif
    </div>
    <div class="kop-text">
        <div class="nama-sekolah">{{ $sekolah->nama_sekolah ?? 'NAMA SEKOLAH' }}</div>
        <div class="alamat-sekolah">
            {{ $sekolah->alamat ?? '' }}
            @if($sekolah->no_telp ?? null) | Telp: {{ $sekolah->no_telp }} @endif
            @if($sekolah->email ?? null) | Email: {{ $sekolah->email }} @endif
        </div>
    </div>
    <div class="kop-pasfoto">
        <div class="box-foto">
            @if($pasfotoPath)
                <img src="{{ $pasfotoPath }}">
            @else
                Pasfoto
            @endif
        </div>
        <div style="font-size:7.5pt;margin-top:2px;">3×4 cm</div>
    </div>
</div>

{{-- ══ JUDUL ══ --}}
<div class="judul">Formulir Pendaftaran Peserta Didik Baru (PPDB)</div>

{{-- Nomor Pendaftaran --}}
<div class="no-pendaftaran">
    No. Pendaftaran: <strong>{{ str_pad($murid->id, 6, '0', STR_PAD_LEFT) }}</strong>
    &nbsp;&nbsp;| Tanggal Daftar: <strong>{{ $murid->created_at ? \Carbon\Carbon::parse($murid->created_at)->translatedFormat('d F Y') : '-' }}</strong>
    &nbsp;&nbsp;| Status: <strong>{{ strtoupper($murid->status ?? '-') }}</strong>
</div>

{{-- ══ I. DATA CALON PESERTA DIDIK ══ --}}
<div class="section-header">I. DATA CALON PESERTA DIDIK</div>
<table class="tabel-data">
    <tr>
        <td class="lbl">Nama Lengkap</td><td class="sep">:</td>
        <td class="val"><strong>{{ strtoupper($murid->nama_lengkap ?? '-') }}</strong></td>
    </tr>
    <tr>
        <td class="lbl">Jenis Kelamin</td><td class="sep">:</td>
        <td class="val">{{ ucfirst($murid->jenis_kelamin ?? '-') }}</td>
    </tr>
    <tr>
        <td class="lbl">NISN</td><td class="sep">:</td>
        <td class="val">{{ $murid->nisn ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">NIS Lama (Sekolah Asal)</td><td class="sep">:</td>
        <td class="val">{{ $murid->nis_lama ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">NIS Baru</td><td class="sep">:</td>
        <td class="val"><strong>{{ $murid->nis_baru ?? '-' }}</strong></td>
    </tr>
    <tr>
        <td class="lbl">NIK</td><td class="sep">:</td>
        <td class="val">{{ $murid->nik ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Tempat, Tanggal Lahir</td><td class="sep">:</td>
        <td class="val">{{ $murid->tempat_lahir ?? '-' }},
            {{ $murid->tgl_lahir ? \Carbon\Carbon::parse($murid->tgl_lahir)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td class="lbl">Sekolah Asal</td><td class="sep">:</td>
        <td class="val">{{ $murid->sekolah_asal ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">No. HP / WhatsApp</td><td class="sep">:</td>
        <td class="val">{{ $murid->no_hp ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Alamat Email</td><td class="sep">:</td>
        <td class="val">{{ $murid->alamat_email ?? '-' }}</td>
    </tr>
</table>

{{-- ══ II. ALAMAT DOMISILI ══ --}}
<div class="section-header">II. ALAMAT DOMISILI</div>
<table class="tabel-data">
    <tr>
        <td class="lbl">Alamat Lengkap</td><td class="sep">:</td>
        <td class="val">{{ $murid->alamat_detail ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">RT/RW</td><td class="sep">:</td>
        <td class="val" style="width:20%;">{{ $murid->rt_rw ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Desa/Kelurahan</td><td class="sep">:</td>
        <td class="val">{{ $murid->desa_kelurahan ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Kota/Kabupaten</td><td class="sep">:</td>
        <td class="val">{{ $murid->kota_kabupaten ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Provinsi</td><td class="sep">:</td>
        <td class="val">{{ $murid->provinsi ?? '-' }}</td>
    </tr>
    <tr>
        <td class="lbl">Transportasi</td><td class="sep">:</td>
        <td class="val">{{ $murid->transportasi ?? '-' }}</td>
    </tr>
</table>

{{-- ══ III. DATA KELUARGA ══ --}}
<div class="section-header">III. DATA KELUARGA</div>
<table class="tabel-data">
    <tr>
        <td class="lbl">Tinggi / Berat Badan</td><td class="sep">:</td>
        <td class="val">{{ $murid->tinggi_badan ?? '-' }} cm / {{ $murid->berat_badan ?? '-' }} kg</td>
    </tr>
    <tr>
        <td class="lbl">Anak Ke</td><td class="sep">:</td>
        <td class="val">{{ $murid->anak_ke ?? '-' }} dari {{ $murid->jlm_saudara ?? '-' }} bersaudara</td>
    </tr>
    <tr>
        <td class="lbl">Jumlah Kakak / Adik</td><td class="sep">:</td>
        <td class="val">{{ $murid->jumlah_kakak ?? '-' }} / {{ $murid->jumlah_adik ?? '-' }}</td>
    </tr>
</table>

{{-- ══ IV. DATA ORANG TUA ══ --}}
<div class="section-header">IV. DATA ORANG TUA / WALI</div>
@php $ortu = $murid->ortu; $wali = $murid->wali; @endphp
<table class="tabel-2col">
<tr>
<td>
    <strong style="font-size:9pt;">A. Data Ayah Kandung</strong>
    <table class="tabel-data" style="margin-top:4px;">
        <tr><td class="lbl">Nama Ayah</td><td class="sep">:</td><td class="val">{{ $ortu->nama_ayah ?? '-' }}</td></tr>
        <tr><td class="lbl">Tempat, Tgl Lahir</td><td class="sep">:</td>
            <td class="val">{{ $ortu->tempat_lahir_ayah ?? '-' }},
                {{ ($ortu->tgl_lahir_ayah ?? null) ? \Carbon\Carbon::parse($ortu->tgl_lahir_ayah)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr><td class="lbl">Pendidikan</td><td class="sep">:</td><td class="val">{{ $ortu->pendidikan_ayah ?? '-' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $ortu->pekerjaan_ayah ?? '-' }}</td></tr>
        <tr><td class="lbl">Penghasilan/bln</td><td class="sep">:</td>
            <td class="val">{{ ($ortu->penghasilan_ayah ?? null) ? 'Rp '.number_format($ortu->penghasilan_ayah,0,',','.') : '-' }}</td>
        </tr>
        <tr><td class="lbl">Status</td><td class="sep">:</td><td class="val">{{ ucfirst($ortu->status_ayah ?? '-') }}</td></tr>
    </table>
</td>
<td>
    <strong style="font-size:9pt;">B. Data Ibu Kandung</strong>
    <table class="tabel-data" style="margin-top:4px;">
        <tr><td class="lbl">Nama Ibu</td><td class="sep">:</td><td class="val">{{ $ortu->nama_ibu ?? '-' }}</td></tr>
        <tr><td class="lbl">Tempat, Tgl Lahir</td><td class="sep">:</td>
            <td class="val">{{ $ortu->tempat_lahir_ibu ?? '-' }},
                {{ ($ortu->tgl_lahir_ibu ?? null) ? \Carbon\Carbon::parse($ortu->tgl_lahir_ibu)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr><td class="lbl">Pendidikan</td><td class="sep">:</td><td class="val">{{ $ortu->pendidikan_ibu ?? '-' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $ortu->pekerjaan_ibu ?? '-' }}</td></tr>
        <tr><td class="lbl">Penghasilan/bln</td><td class="sep">:</td>
            <td class="val">{{ ($ortu->penghasilan_ibu ?? null) ? 'Rp '.number_format($ortu->penghasilan_ibu,0,',','.') : '-' }}</td>
        </tr>
        <tr><td class="lbl">Status</td><td class="sep">:</td><td class="val">{{ ucfirst($ortu->status_ibu ?? '-') }}</td></tr>
    </table>
</td>
</tr>
</table>

@if($wali && $wali->nama_wali)
<div style="margin-top:6px;">
    <strong style="font-size:9pt;">C. Data Wali Murid</strong>
    <table class="tabel-data" style="margin-top:4px;">
        <tr><td class="lbl">Nama Wali</td><td class="sep">:</td><td class="val">{{ $wali->nama_wali }}</td></tr>
        <tr><td class="lbl">Hubungan</td><td class="sep">:</td><td class="val">{{ $wali->hubungan_wali ?? '-' }}</td></tr>
        <tr><td class="lbl">Pekerjaan</td><td class="sep">:</td><td class="val">{{ $wali->pekerjaan_wali ?? '-' }}</td></tr>
        <tr><td class="lbl">Penghasilan/bln</td><td class="sep">:</td>
            <td class="val">{{ ($wali->penghasilan_wali ?? null) ? 'Rp '.number_format($wali->penghasilan_wali,0,',','.') : '-' }}</td>
        </tr>
    </table>
</div>
@endif

{{-- ══ V. KELENGKAPAN DOKUMEN ══ --}}
<div class="section-header">V. KELENGKAPAN DOKUMEN YANG DISERAHKAN</div>
<table class="checklist-table">
    @php
        $dokLabels = [
            'Pasfoto'                       => isset($dokumenDiupload['Pasfoto'])                       && $dokumenDiupload['Pasfoto'],
            'KTP Ayah'                      => isset($dokumenDiupload['KTP Ayah'])                      && $dokumenDiupload['KTP Ayah'],
            'KTP Ibu'                       => isset($dokumenDiupload['KTP Ibu'])                       && $dokumenDiupload['KTP Ibu'],
            'KTP Wali'                      => isset($dokumenDiupload['KTP Wali'])                      && $dokumenDiupload['KTP Wali'],
            'Kartu Keluarga'                => isset($dokumenDiupload['Kartu Keluarga'])                && $dokumenDiupload['Kartu Keluarga'],
            'Akte Kelahiran'                => isset($dokumenDiupload['Akte Kelahiran'])                && $dokumenDiupload['Akte Kelahiran'],
            'Ijazah Terakhir'               => isset($dokumenDiupload['Ijazah Terakhir'])               && $dokumenDiupload['Ijazah Terakhir'],
            'Transkip Nilai'                => isset($dokumenDiupload['Transkip Nilai'])                && $dokumenDiupload['Transkip Nilai'],
            'Surat Kelulusan'               => isset($dokumenDiupload['Surat Kelulusan'])               && $dokumenDiupload['Surat Kelulusan'],
            'Surat Ket. Hasil Ujian'        => isset($dokumenDiupload['Surat Keterangan Hasil Ujian'])  && $dokumenDiupload['Surat Keterangan Hasil Ujian'],
            'Surat Pindahan'                => isset($dokumenDiupload['Surat Pindahan'])                && $dokumenDiupload['Surat Pindahan'],
            'Formulir Fisik'                => isset($dokumenDiupload['Formulir Fisik'])                && $dokumenDiupload['Formulir Fisik'],
        ];
        $chunks = array_chunk(array_keys($dokLabels), 4, true);
    @endphp
    @foreach($chunks as $row)
    <tr>
        @foreach($row as $label)
        <td style="width:25%;">
            <span class="box-check {{ $dokLabels[$label] ? 'checked' : '' }}">{{ $dokLabels[$label] ? '✓' : '' }}</span>
            {{ $label }}
        </td>
        @endforeach
        @for($i = count($row); $i < 4; $i++) <td></td> @endfor
    </tr>
    @endforeach
</table>

{{-- ══ VI. STATUS PEMBAYARAN ══ --}}
<div class="section-header">VI. STATUS PEMBAYARAN</div>
<table class="checklist-table">
    @php
        $biayaChunks = $biayas->chunk(2);
    @endphp
    @foreach($biayaChunks as $row)
    <tr>
        @foreach($row as $b)
        @php
            $lunas = $b->is_active; // anggap aktif = sudah ditagihkan; bisa dikembangkan dengan tabel pembayaran
            $metode = 'Cash';
            if ($b->account) {
                $metode = $b->account->is_qris ? 'QRIS' : 'Transfer ('.$b->account->bank_name.')';
            }
        @endphp
        <td style="width:50%;">
            <span class="box-check {{ $lunas ? 'checked' : '' }}">{{ $lunas ? '✓' : '' }}</span>
            <strong>{{ $b->name }}</strong>
            — Rp {{ number_format($b->amount, 0, ',', '.') }}
            <span style="font-size:8pt;color:#555;">({{ $metode }})</span>
        </td>
        @endforeach
        @if($row->count() < 2) <td></td> @endif
    </tr>
    @endforeach
    @if($biayas->isEmpty())
    <tr><td colspan="2" style="color:#999;font-size:8.5pt;">Belum ada data pembayaran.</td></tr>
    @endif
</table>

{{-- ══ TANDA TANGAN ══ --}}
<div class="ttd-row">
    <div class="ttd-col">
        <div>Orang Tua / Wali</div>
        <div class="ttd-space"></div>
        <div class="ttd-line">(____________________)</div>
    </div>
    <div class="ttd-col">
        <div>{{ $sekolah->kota_kabupaten ?? '................' }},
            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
        <div style="margin-top:4px;">Peserta Didik,</div>
        <div class="ttd-space"></div>
        <div class="ttd-line">( {{ strtoupper($murid->nama_lengkap ?? '...........') }} )</div>
    </div>
    <div class="ttd-col">
        <div>Petugas Pendaftaran,</div>
        <div class="ttd-space"></div>
        <div class="ttd-line">(____________________)</div>
    </div>
</div>

{{-- Footer --}}
<div class="footer-note">
    * Dokumen ini dicetak secara otomatis oleh Sistem Informasi Manajemen Sekolah.
    Harap disertakan stempel dan tanda tangan asli sebelum dijadikan dokumen resmi.
</div>

</body>
</html>
