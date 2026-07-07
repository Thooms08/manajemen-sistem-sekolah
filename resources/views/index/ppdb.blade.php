<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Siswa Baru (PPDB)</title>
        @include('favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .ppdb-container { max-width: 950px; margin: 40px auto; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .step-header { border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; }
        .btn-success { background-color: #198754; border: none; padding: 12px 30px; font-weight: bold; border-radius: 10px; }
        .hidden { display: none; }
        .form-label { font-weight: 600; color: #444; font-size: 0.9rem; }
        .form-control, .form-select { border-radius: 8px; padding: 10px; border: 1px solid #ddd; }
        .form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.1); }
        .step-indicator { display: flex; justify-content: center; margin-bottom: 30px; }
        .step-indicator .step { 
            width: 40px; height: 40px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center;
            margin: 0 10px; font-weight: bold; cursor: pointer;
            transition: all 0.3s;
        }
        .step-indicator .step.active { background-color: #198754; color: white; }
        .step-indicator .step.completed { background-color: #28a745; color: white; }
        .step-indicator .step.inactive { background-color: #e9ecef; color: #6c757d; }
        .payment-card { border: 1px solid #dee2e6; border-radius: 10px; padding: 15px; margin-bottom: 15px; }
        .payment-card.active { border-color: #198754; background-color: #f8fff9; }
        .payment-card.disabled { background-color: #f8f9fa; border-color: #dee2e6; }

        /* Upload bukti pembayaran */
        .bukti-upload-area { border: 2px dashed #198754; border-radius: 10px; padding: 14px 16px; background: #f8fff9; margin-top: 14px; }
        .bukti-upload-area .upload-label { font-size: 0.83rem; font-weight: 600; color: #198754; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .bukti-upload-area input[type="file"] { font-size: 0.83rem; }
        .bukti-preview-wrap { margin-top: 10px; display: none; }
        .bukti-preview-wrap img { max-height: 160px; max-width: 100%; border-radius: 8px; border: 2px solid #198754; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .bukti-preview-wrap .btn-hapus-bukti { font-size: 0.75rem; margin-top: 6px; }
        .bukti-error { font-size: 0.78rem; color: #dc3545; margin-top: 4px; display: none; }
        .qris-image { max-width: 200px; height: auto; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
    </style>
</head>
<body>
    @include('loading')
    <div class="container ppdb-container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-success">FORMULIR PENDAFTARAN SISWA BARU</h2>
            <p class="text-muted">Lengkapi seluruh data dengan benar untuk memproses verifikasi pendaftaran.</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ppdb.store') }}" method="POST" enctype="multipart/form-data" id="ppdbForm" data-form-settings="{{ json_encode($formSettings) }}">
            @csrf

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="indicator1" onclick="goToStep(1)">1</div>
                <div class="step inactive" id="indicator2" onclick="goToStep(2)">2</div>
                <div class="step inactive" id="indicator3" onclick="goToStep(3)">3</div>
                <div class="step inactive" id="indicator4" onclick="goToStep(4)">4</div>
                <div class="step inactive" id="indicator5" onclick="goToStep(5)">5</div>
                <div class="step inactive" id="indicator6" onclick="goToStep(6)">6</div>
            </div>

            <!-- Step 1: Data Calon Murid -->
            <div id="step1" class="card p-4">
                <h5 class="step-header text-success fw-bold"><i class="bi bi-1-circle-fill me-2"></i>Data Calon Murid</h5>
                <div class="row g-3">
                    @if(isset($formSettings['nama_lengkap']) && $formSettings['nama_lengkap']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control auto-save-input" value="{{ old('nama_lengkap') }}" {{ $formSettings['nama_lengkap']->is_required ? 'required' : '' }}>
                    </div>
                    @endif
                    @if(isset($formSettings['jenis_kelamin']) && $formSettings['jenis_kelamin']->is_active)
                    <div class="col-md-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" {{ $formSettings['jenis_kelamin']->is_required ? 'required' : '' }}>
                            <option value="">- Pilih -</option>
                            <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    @endif
                    @if(isset($formSettings['nisn']) && $formSettings['nisn']->is_active)
                    <div class="col-md-3">
                        <label class="form-label">NISN (10 Digit)</label>
                        <input type="text" name="nisn" id="nisn" class="form-control" maxlength="10" value="{{ old('nisn') }}" {{ $formSettings['nisn']->is_required ? 'required' : '' }} oninput="validateNISN(this)">
                        <div id="nisn-alert" class="form-text mt-1"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['nik']) && $formSettings['nik']->is_active)
                    <div class="col-md-4">
                        <label class="form-label">NIK (16 Digit)</label>
                        <input type="text" name="nik" id="nik" class="form-control" maxlength="16" value="{{ old('nik') }}" {{ $formSettings['nik']->is_required ? 'required' : '' }} oninput="validateNIK(this)">
                        <div id="nik-alert" class="form-text mt-1"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['tempat_lahir']) && $formSettings['tempat_lahir']->is_active)
                    <div class="col-md-4">
                        <label class="form-label">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" {{ $formSettings['tempat_lahir']->is_required ? 'required' : '' }}>
                    </div>
                    @endif
                    @if(isset($formSettings['tgl_lahir']) && $formSettings['tgl_lahir']->is_active)
                    <div class="col-md-4">
                        <label class="form-label">Tgl Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}" {{ $formSettings['tgl_lahir']->is_required ? 'required' : '' }}>
                    </div>
                    @endif
                    @if(isset($formSettings['rt_rw']) && $formSettings['rt_rw']->is_active)
                    <div class="col-md-3"><label class="form-label">RT/RW</label><input type="text" name="rt_rw" class="form-control" value="{{ old('rt_rw') }}" {{ $formSettings['rt_rw']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['desa_kelurahan']) && $formSettings['desa_kelurahan']->is_active)
                    <div class="col-md-3"><label class="form-label">Desa/Kelurahan</label><input type="text" name="desa_kelurahan" class="form-control" value="{{ old('desa_kelurahan') }}" {{ $formSettings['desa_kelurahan']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['kota_kabupaten']) && $formSettings['kota_kabupaten']->is_active)
                    <div class="col-md-3"><label class="form-label">Kota/Kabupaten</label><input type="text" name="kota_kabupaten" class="form-control" value="{{ old('kota_kabupaten') }}" {{ $formSettings['kota_kabupaten']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['provinsi']) && $formSettings['provinsi']->is_active)
                    <div class="col-md-3"><label class="form-label">Provinsi</label><input type="text" name="provinsi" class="form-control" value="{{ old('provinsi') }}" {{ $formSettings['provinsi']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['alamat_detail']) && $formSettings['alamat_detail']->is_active)
                    <div class="col-12"><label class="form-label">Alamat Detail</label><textarea name="alamat_detail" class="form-control" rows="2" placeholder="Nama jalan, nomor rumah, dsb..." {{ $formSettings['alamat_detail']->is_required ? 'required' : '' }}>{{ old('alamat_detail') }}</textarea></div>
                    @endif
                    @if(isset($formSettings['transportasi']) && $formSettings['transportasi']->is_active)
                    <div class="col-md-4"><label class="form-label">Transportasi</label><input type="text" name="transportasi" class="form-control" value="{{ old('transportasi') }}" {{ $formSettings['transportasi']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['no_hp']) && $formSettings['no_hp']->is_active)
                    <div class="col-md-4"><label class="form-label">No. HP (WhatsApp)</label><input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" {{ $formSettings['no_hp']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['alamat_email']) && $formSettings['alamat_email']->is_active)
                    <div class="col-md-4"><label class="form-label">Email Aktif</label><input type="email" name="alamat_email" class="form-control" value="{{ old('alamat_email') }}" {{ $formSettings['alamat_email']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['sekolah_asal']) && $formSettings['sekolah_asal']->is_active)
                    <div class="col-md-6"><label class="form-label">Sekolah Asal</label><input type="text" name="sekolah_asal" class="form-control" value="{{ old('sekolah_asal') }}" {{ $formSettings['sekolah_asal']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tinggi_badan']) && $formSettings['tinggi_badan']->is_active)
                    <div class="col-md-3"><label class="form-label">Tinggi (cm)</label><input type="number" step="0.1" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan') }}" {{ $formSettings['tinggi_badan']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['berat_badan']) && $formSettings['berat_badan']->is_active)
                    <div class="col-md-3"><label class="form-label">Berat (kg)</label><input type="number" step="0.1" name="berat_badan" class="form-control" value="{{ old('berat_badan') }}" {{ $formSettings['berat_badan']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['anak_ke']) && $formSettings['anak_ke']->is_active)
                    <div class="col-md-3"><label class="form-label">Anak Ke</label><input type="number" name="anak_ke" class="form-control" value="{{ old('anak_ke') }}" {{ $formSettings['anak_ke']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['jlm_saudara']) && $formSettings['jlm_saudara']->is_active)
                    <div class="col-md-3"><label class="form-label">Jml Saudara</label><input type="number" name="jlm_saudara" class="form-control" value="{{ old('jlm_saudara') }}" {{ $formSettings['jlm_saudara']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['jumlah_kakak']) && $formSettings['jumlah_kakak']->is_active)
                    <div class="col-md-3"><label class="form-label">Jml Kakak</label><input type="number" name="jumlah_kakak" class="form-control" value="{{ old('jumlah_kakak') }}" {{ $formSettings['jumlah_kakak']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['jumlah_adik']) && $formSettings['jumlah_adik']->is_active)
                    <div class="col-md-3"><label class="form-label">Jml Adik</label><input type="number" name="jumlah_adik" class="form-control" value="{{ old('jumlah_adik') }}" {{ $formSettings['jumlah_adik']->is_required ? 'required' : '' }}></div>
                    @endif
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="/" class="btn btn-outline-secondary px-3 d-flex align-items-center rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i> Batal
                    </a>
                    <button type="button" class="btn btn-success px-3 rounded-pill shadow-sm" onclick="showStep2()">
                        Lanjut: Data Ortu <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Data Orang Tua -->
            <div id="step2" class="card p-4 hidden">
                <h5 class="step-header text-success fw-bold"><i class="bi bi-2-circle-fill me-2"></i>Data Orang Tua</h5>
                <div class="row g-3">
                    <h6 class="fw-bold text-muted border-start border-success border-3 ps-2">Data Ayah Kandung</h6>
                    @if(isset($formSettings['nama_ayah']) && $formSettings['nama_ayah']->is_active)
                    <div class="col-md-4"><label class="form-label">Nama Ayah</label><input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}" {{ $formSettings['nama_ayah']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tempat_lahir_ayah']) && $formSettings['tempat_lahir_ayah']->is_active)
                    <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir_ayah" class="form-control" value="{{ old('tempat_lahir_ayah') }}" {{ $formSettings['tempat_lahir_ayah']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tgl_lahir_ayah']) && $formSettings['tgl_lahir_ayah']->is_active)
                    <div class="col-md-4"><label class="form-label">Tgl Lahir</label><input type="date" name="tgl_lahir_ayah" class="form-control" value="{{ old('tgl_lahir_ayah') }}" {{ $formSettings['tgl_lahir_ayah']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['pendidikan_ayah']) && $formSettings['pendidikan_ayah']->is_active)
                    <div class="col-md-3"><label class="form-label">Pendidikan</label><input type="text" name="pendidikan_ayah" class="form-control" value="{{ old('pendidikan_ayah') }}" {{ $formSettings['pendidikan_ayah']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['pekerjaan_ayah']) && $formSettings['pekerjaan_ayah']->is_active)
                    <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah') }}" {{ $formSettings['pekerjaan_ayah']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['penghasilan_ayah']) && $formSettings['penghasilan_ayah']->is_active)
                    <div class="col-md-3"><label class="form-label">Penghasilan</label><input type="number" name="penghasilan_ayah" class="form-control" value="{{ old('penghasilan_ayah') }}" {{ $formSettings['penghasilan_ayah']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['status_ayah']) && $formSettings['status_ayah']->is_active)
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status_ayah" class="form-select" {{ $formSettings['status_ayah']->is_required ? 'required' : '' }}>
                            <option value="">- Pilih -</option>
                            <option value="hidup" {{ old('status_ayah') == 'hidup' ? 'selected' : '' }}>Hidup</option>
                            <option value="meninggal" {{ old('status_ayah') == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                        </select>
                    </div>
                    @endif

                    <hr class="my-4">
                    
                    <h6 class="fw-bold text-muted border-start border-success border-3 ps-2">Data Ibu Kandung</h6>
                    @if(isset($formSettings['nama_ibu']) && $formSettings['nama_ibu']->is_active)
                    <div class="col-md-4"><label class="form-label">Nama Ibu</label><input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}" {{ $formSettings['nama_ibu']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tempat_lahir_ibu']) && $formSettings['tempat_lahir_ibu']->is_active)
                    <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir_ibu" class="form-control" value="{{ old('tempat_lahir_ibu') }}" {{ $formSettings['tempat_lahir_ibu']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tgl_lahir_ibu']) && $formSettings['tgl_lahir_ibu']->is_active)
                    <div class="col-md-4"><label class="form-label">Tgl Lahir</label><input type="date" name="tgl_lahir_ibu" class="form-control" value="{{ old('tgl_lahir_ibu') }}" {{ $formSettings['tgl_lahir_ibu']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['pendidikan_ibu']) && $formSettings['pendidikan_ibu']->is_active)
                    <div class="col-md-3"><label class="form-label">Pendidikan</label><input type="text" name="pendidikan_ibu" class="form-control" value="{{ old('pendidikan_ibu') }}" {{ $formSettings['pendidikan_ibu']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['pekerjaan_ibu']) && $formSettings['pekerjaan_ibu']->is_active)
                    <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu') }}" {{ $formSettings['pekerjaan_ibu']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['penghasilan_ibu']) && $formSettings['penghasilan_ibu']->is_active)
                    <div class="col-md-3"><label class="form-label">Penghasilan</label><input type="number" name="penghasilan_ibu" class="form-control" value="{{ old('penghasilan_ibu') }}" {{ $formSettings['penghasilan_ibu']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['status_ibu']) && $formSettings['status_ibu']->is_active)
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status_ibu" class="form-select" {{ $formSettings['status_ibu']->is_required ? 'required' : '' }}>
                            <option value="">- Pilih -</option>
                            <option value="hidup" {{ old('status_ibu') == 'hidup' ? 'selected' : '' }}>Hidup</option>
                            <option value="meninggal" {{ old('status_ibu') == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                        </select>
                    </div>
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" onclick="showStep1()">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-success px-3 rounded-pill shadow-sm" onclick="showStep3()">
                        Lanjut: Data Wali <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Data Wali Murid -->
            <div id="step3" class="card p-4 hidden">
                <h5 class="step-header text-success fw-bold"><i class="bi bi-3-circle-fill me-2"></i>Data Wali Murid (Opsional)</h5>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Data wali murid bersifat opsional. Isi hanya jika ada wali yang bertanggung jawab.
                </div>
                <div class="row g-3">
                    @if(isset($formSettings['nama_wali']) && $formSettings['nama_wali']->is_active)
                    <div class="col-md-4"><label class="form-label">Nama Wali</label><input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali') }}" {{ $formSettings['nama_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['hubungan_wali']) && $formSettings['hubungan_wali']->is_active)
                    <div class="col-md-4"><label class="form-label">Hubungan</label><input type="text" name="hubungan_wali" class="form-control" placeholder="Contoh: Kakek, Nenek, Paman" value="{{ old('hubungan_wali') }}" {{ $formSettings['hubungan_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tempat_lahir_wali']) && $formSettings['tempat_lahir_wali']->is_active)
                    <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir_wali" class="form-control" value="{{ old('tempat_lahir_wali') }}" {{ $formSettings['tempat_lahir_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['tgl_lahir_wali']) && $formSettings['tgl_lahir_wali']->is_active)
                    <div class="col-md-4"><label class="form-label">Tgl Lahir</label><input type="date" name="tgl_lahir_wali" class="form-control" value="{{ old('tgl_lahir_wali') }}" {{ $formSettings['tgl_lahir_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['pendidikan_wali']) && $formSettings['pendidikan_wali']->is_active)
                    <div class="col-md-3"><label class="form-label">Pendidikan</label><input type="text" name="pendidikan_wali" class="form-control" value="{{ old('pendidikan_wali') }}" {{ $formSettings['pendidikan_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['pekerjaan_wali']) && $formSettings['pekerjaan_wali']->is_active)
                    <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali') }}" {{ $formSettings['pekerjaan_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['penghasilan_wali']) && $formSettings['penghasilan_wali']->is_active)
                    <div class="col-md-3"><label class="form-label">Penghasilan</label><input type="number" name="penghasilan_wali" class="form-control" value="{{ old('penghasilan_wali') }}" {{ $formSettings['penghasilan_wali']->is_required ? 'required' : '' }}></div>
                    @endif
                    @if(isset($formSettings['status_wali']) && $formSettings['status_wali']->is_active)
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status_wali" class="form-select" {{ $formSettings['status_wali']->is_required ? 'required' : '' }}>
                            <option value="">- Pilih -</option>
                            <option value="hidup" {{ old('status_wali') == 'hidup' ? 'selected' : '' }}>Hidup</option>
                            <option value="meninggal" {{ old('status_wali') == 'meninggal' ? 'selected' : '' }}>Meninggal</option>
                        </select>
                    </div>
                    @endif
                </div>
                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" onclick="showStep2()">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-success px-3 rounded-pill shadow-sm" onclick="showStep4()">
                        Lanjut: Dokumen <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Dokumen PPDB -->
            <div id="step4" class="card p-4 hidden">
                <h5 class="step-header text-success fw-bold"><i class="bi bi-4-circle-fill me-2"></i>Dokumen PPDB (Opsional)</h5>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Upload dokumen bersifat opsional. Format yang diterima: JPG, PNG, PDF (Max 2MB).
                </div>
                <div class="row g-3">
                    @if(isset($formSettings['pasfoto']) && $formSettings['pasfoto']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Pasfoto</label>
                        <input type="file" name="pasfoto" id="pasfoto" class="form-control" accept=".jpg,.jpeg,.png" {{ $formSettings['pasfoto']->is_required ? 'required' : '' }} onchange="previewFile(this, 'pasfoto-preview', 'pasfoto-alert')">
                        <div id="pasfoto-alert" class="form-text mt-1"></div>
                        <div id="pasfoto-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['ktp_ayah']) && $formSettings['ktp_ayah']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">KTP Ayah</label>
                        <input type="file" name="ktp_ayah" id="ktp_ayah" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ktp_ayah']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ktp_ayah-preview', 'ktp_ayah-alert')">
                        <div id="ktp_ayah-alert" class="form-text mt-1"></div>
                        <div id="ktp_ayah-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['ktp_ibu']) && $formSettings['ktp_ibu']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">KTP Ibu</label>
                        <input type="file" name="ktp_ibu" id="ktp_ibu" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ktp_ibu']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ktp_ibu-preview', 'ktp_ibu-alert')">
                        <div id="ktp_ibu-alert" class="form-text mt-1"></div>
                        <div id="ktp_ibu-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['ktp_wali']) && $formSettings['ktp_wali']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">KTP Wali</label>
                        <input type="file" name="ktp_wali" id="ktp_wali" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ktp_wali']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ktp_wali-preview', 'ktp_wali-alert')">
                        <div id="ktp_wali-alert" class="form-text mt-1"></div>
                        <div id="ktp_wali-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['kartu_keluarga']) && $formSettings['kartu_keluarga']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Kartu Keluarga</label>
                        <input type="file" name="kartu_keluarga" id="kartu_keluarga" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['kartu_keluarga']->is_required ? 'required' : '' }} onchange="previewFile(this, 'kartu_keluarga-preview', 'kartu_keluarga-alert')">
                        <div id="kartu_keluarga-alert" class="form-text mt-1"></div>
                        <div id="kartu_keluarga-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['akte_kelahiran']) && $formSettings['akte_kelahiran']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Akte Kelahiran</label>
                        <input type="file" name="akte_kelahiran" id="akte_kelahiran" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['akte_kelahiran']->is_required ? 'required' : '' }} onchange="previewFile(this, 'akte_kelahiran-preview', 'akte_kelahiran-alert')">
                        <div id="akte_kelahiran-alert" class="form-text mt-1"></div>
                        <div id="akte_kelahiran-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['ijazah_terakhir']) && $formSettings['ijazah_terakhir']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Ijazah Terakhir</label>
                        <input type="file" name="ijazah_terakhir" id="ijazah_terakhir" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ijazah_terakhir']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ijazah_terakhir-preview', 'ijazah_terakhir-alert')">
                        <div id="ijazah_terakhir-alert" class="form-text mt-1"></div>
                        <div id="ijazah_terakhir-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['transkip_nilai']) && $formSettings['transkip_nilai']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Transkip Nilai</label>
                        <input type="file" name="transkip_nilai" id="transkip_nilai" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['transkip_nilai']->is_required ? 'required' : '' }} onchange="previewFile(this, 'transkip_nilai-preview', 'transkip_nilai-alert')">
                        <div id="transkip_nilai-alert" class="form-text mt-1"></div>
                        <div id="transkip_nilai-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['surat_kelulusan']) && $formSettings['surat_kelulusan']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Surat Kelulusan</label>
                        <input type="file" name="surat_kelulusan" id="surat_kelulusan" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['surat_kelulusan']->is_required ? 'required' : '' }} onchange="previewFile(this, 'surat_kelulusan-preview', 'surat_kelulusan-alert')">
                        <div id="surat_kelulusan-alert" class="form-text mt-1"></div>
                        <div id="surat_kelulusan-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['surat_keterangan_hasil_ujian']) && $formSettings['surat_keterangan_hasil_ujian']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Surat Keterangan Hasil Ujian</label>
                        <input type="file" name="surat_keterangan_hasil_ujian" id="surat_keterangan_hasil_ujian" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['surat_keterangan_hasil_ujian']->is_required ? 'required' : '' }} onchange="previewFile(this, 'surat_keterangan_hasil_ujian-preview', 'surat_keterangan_hasil_ujian-alert')">
                        <div id="surat_keterangan_hasil_ujian-alert" class="form-text mt-1"></div>
                        <div id="surat_keterangan_hasil_ujian-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['surat_pindahan']) && $formSettings['surat_pindahan']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Surat Pindahan</label>
                        <input type="file" name="surat_pindahan" id="surat_pindahan" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['surat_pindahan']->is_required ? 'required' : '' }} onchange="previewFile(this, 'surat_pindahan-preview', 'surat_pindahan-alert')">
                        <div id="surat_pindahan-alert" class="form-text mt-1"></div>
                        <div id="surat_pindahan-preview" class="mt-2"></div>
                    </div>
                    @endif
                    @if(isset($formSettings['formulir_fisik']) && $formSettings['formulir_fisik']->is_active)
                    <div class="col-md-6">
                        <label class="form-label">Formulir Fisik</label>
                        <input type="file" name="formulir_fisik" id="formulir_fisik" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['formulir_fisik']->is_required ? 'required' : '' }} onchange="previewFile(this, 'formulir_fisik-preview', 'formulir_fisik-alert')">
                        <div id="formulir_fisik-alert" class="form-text mt-1"></div>
                        <div id="formulir_fisik-preview" class="mt-2"></div>
                    </div>
                    @endif
                </div>
                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" onclick="showStep3()">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-success px-3 rounded-pill shadow-sm" onclick="showStep5()">
                        Lanjut: Biaya <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 5: Biaya Pendaftaran -->
            <div id="step5" class="card p-4 hidden">
                <h5 class="step-header text-success fw-bold"><i class="bi bi-5-circle-fill me-2"></i>Biaya Pendaftaran</h5>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Berikut adalah daftar biaya yang harus dibayar sesuai pengaturan sekolah.
                </div>
                @if($biayas->count() > 0)
                    @foreach($biayas as $biaya)
                        <div class="payment-card @if($biaya->account) @if($biaya->account->is_qris)qris-payment @else transfer-payment @endif @else cash-payment @endif @if(!$biaya->is_active)disabled @endif" @if(!$biaya->is_active)style="opacity: 0.6;"@endif>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $biaya->name }}</h6>
                                    <p class="text-success fw-bold mb-0">Rp {{ number_format($biaya->amount, 0, ',', '.') }}</p>
                                    @if(!$biaya->is_active && $biaya->disabled_reason)
                                        <small class="text-danger"><i class="bi bi-exclamation-circle"></i> {{ $biaya->disabled_reason }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    @if($biaya->is_active)
                                        @if($biaya->account)
                                            @if($biaya->account->is_qris)
                                                <span class="badge bg-primary">QRIS</span>
                                                @if($biaya->account->qris_image)
                                                    <img src="{{ asset($biaya->account->qris_image) }}" class="qris-image mt-2" alt="QRIS">
                                                @endif
                                            @else
                                                <span class="badge bg-info">Transfer</span>
                                                <small class="d-block">{{ $biaya->account->bank_name }}</small>
                                                <small class="d-block">{{ $biaya->account->account_number }}</small>
                                                <small class="d-block">a.n {{ $biaya->account->account_holder }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-success">Cash</span>
                                            <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;">
                                                <i class="bi bi-info-circle me-1"></i>Pembayaran dilakukan di sekolah
                                            </p>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">Dinonaktifkan</span>
                                        <small class="d-block text-muted">Pembayaran ini tidak tersedia saat ini</small>
                                    @endif
                                </div>
                            </div>

                            {{-- ── Upload Bukti Pembayaran: hanya muncul untuk QRIS/Transfer ── --}}
                            @if($biaya->is_active && $biaya->account)
                            <div class="bukti-upload-area">
                                <div class="upload-label">
                                    <i class="bi bi-upload"></i>
                                    Upload Bukti Pembayaran
                                    <span class="badge bg-danger ms-1" style="font-size:0.68rem;">Wajib</span>
                                </div>
                                <div style="font-size:0.78rem; color:#6c757d; margin-bottom:8px;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Setelah melakukan pembayaran via
                                    {{ $biaya->account->is_qris ? 'QRIS' : 'Transfer ('.$biaya->account->bank_name.')' }},
                                    upload screenshot / foto bukti transfer di sini.
                                </div>
                                <input
                                    type="file"
                                    name="bukti_pembayaran[{{ $biaya->id }}]"
                                    id="bukti_{{ $biaya->id }}"
                                    class="form-control form-control-sm bukti-input"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    data-biaya-id="{{ $biaya->id }}"
                                    data-biaya-name="{{ $biaya->name }}"
                                    onchange="handleBuktiUpload(this)"
                                >
                                <div class="bukti-error" id="bukti-error-{{ $biaya->id }}">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    <span></span>
                                </div>
                                {{-- Preview gambar --}}
                                <div class="bukti-preview-wrap" id="bukti-preview-{{ $biaya->id }}">
                                    <img src="" alt="Preview Bukti" id="bukti-preview-img-{{ $biaya->id }}">
                                    <br>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus-bukti"
                                            onclick="hapusBukti('{{ $biaya->id }}')">
                                        <i class="bi bi-trash me-1"></i>Hapus Foto
                                    </button>
                                </div>
                            </div>
                            @endif
                            {{-- ── End Upload Bukti ── --}}

                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Belum ada biaya yang diatur oleh admin.
                    </div>
                @endif
                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" onclick="showStep4()">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-success px-3 rounded-pill shadow-sm" onclick="showStep6()">
                        Lanjut: Konfirmasi <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 6: Konfirmasi Pendaftaran -->
            <div id="step6" class="card p-4 hidden">
                <h5 class="step-header text-success fw-bold"><i class="bi bi-6-circle-fill me-2"></i>Konfirmasi Pendaftaran</h5>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>Mohon periksa kembali seluruh data sebelum mengirim pendaftaran.
                </div>
                
                <!-- Summary will be populated by JavaScript -->
                <div id="confirmationSummary"></div>
                
                <div class="d-flex justify-content-between mt-5">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-pill" onclick="showStep5()">
                        <i class="bi bi-arrow-left me-2"></i> Kembali
                    </button>
                    <button type="submit" class="btn btn-success px-3 rounded-pill shadow-sm">
                        Kirim Pendaftaran <i class="bi bi-send-fill ms-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 6;

        function updateIndicators() {
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById('indicator' + i);
                indicator.classList.remove('active', 'completed', 'inactive');
                if (i < currentStep) {
                    indicator.classList.add('completed');
                } else if (i === currentStep) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.add('inactive');
                }
            }
        }

        function showStep(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const stepEl = document.getElementById('step' + i);
                if (i === step) {
                    stepEl.classList.remove('hidden');
                    // Re-enable semua input di step yang aktif
                    stepEl.querySelectorAll('input, select, textarea').forEach(el => {
                        el.disabled = false;
                    });
                } else {
                    stepEl.classList.add('hidden');
                    // Disable semua input di step yang tidak aktif
                    // agar tidak divalidasi oleh browser saat form disubmit
                    stepEl.querySelectorAll('input, select, textarea').forEach(el => {
                        el.disabled = true;
                    });
                }
            }
            currentStep = step;
            updateIndicators();
            window.scrollTo(0, 0);
        }

        function goToStep(step) {
            if (step <= currentStep || confirm('Apakah Anda yakin ingin melompat ke langkah ' + step + '?')) {
                showStep(step);
            }
        }

        // ── Validasi field required di step aktif ──────────────────────────
         function validateStep(stepNum) {
            const stepEl = document.getElementById('step' + stepNum);
            stepEl.querySelectorAll('.field-required-alert').forEach(el => el.remove());
            stepEl.querySelectorAll('.is-invalid-required').forEach(el => {
                el.classList.remove('is-invalid-required');
                el.style.borderColor = '';
            });

            let firstInvalid = null;
            let hasError = false;

            // Cek semua input/select/textarea yang punya atribut required dan tidak disabled
            stepEl.querySelectorAll('input[required]:not([disabled]), select[required]:not([disabled]), textarea[required]:not([disabled])').forEach(field => {
                const isEmpty = (field.type === 'file')
                    ? (!field.files || field.files.length === 0)
                    : (field.value.trim() === '');

                // Hapus error lama spesifik field ini dulu
                const existingAlert = field.parentElement.querySelector('.field-required-alert');
                if (existingAlert) existingAlert.remove();

                if (isEmpty) {
                    hasError = true;
                    // Beri border merah pada field
                    field.style.borderColor = '#dc3545';
                    field.classList.add('is-invalid-required');

                    // Ambil label untuk pesan
                    const labelEl = field.closest('.col-md-3, .col-md-4, .col-md-6, .col-12, .col')
                                  ?.querySelector('label');
                    const labelText = labelEl ? labelEl.textContent.trim() : 'Field ini';

                    // Buat elemen alert di bawah field
                    const alert = document.createElement('div');
                    alert.className = 'field-required-alert text-danger mt-1';
                    alert.style.fontSize = '0.8rem';
                    alert.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + labelText + ' wajib diisi.';
                    field.insertAdjacentElement('afterend', alert);

                    if (!firstInvalid) firstInvalid = field;
                } else {
                    // Reset border jika sudah diisi
                    field.style.borderColor = '';
                    field.classList.remove('is-invalid-required');
                }
            });

            stepEl.querySelectorAll('input[type="file"]').forEach(field => {
                if (field.classList.contains('is-invalid')) {
                    hasError = true;
                    if (!firstInvalid) firstInvalid = field;
                }
            });

            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }

            return !hasError;
        }

        // Hapus error pada field saat user mulai mengetik/memilih
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('is-invalid-required')) {
                e.target.style.borderColor = '';
                e.target.classList.remove('is-invalid-required');
                const alert = e.target.parentElement.querySelector('.field-required-alert');
                if (alert) alert.remove();
            }
        });
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('is-invalid-required')) {
                e.target.style.borderColor = '';
                e.target.classList.remove('is-invalid-required');
                const alert = e.target.parentElement.querySelector('.field-required-alert');
                if (alert) alert.remove();
            }
        });

        function showStep2() { if (validateStep(1)) showStep(2); }
        function showStep3() { if (validateStep(2)) showStep(3); }
        function showStep4() { if (validateStep(3)) showStep(4); }
        function showStep5() { if (validateStep(4)) showStep(5); }
        function showStep6() {
            if (validateStep(5)) {
                // Cek apakah semua bukti pembayaran non-cash sudah diupload
                if (!validateBuktiPembayaran()) return;
                generateConfirmationSummary();
                showStep(6);
            }
        }
        function showStep1() { showStep(1); }

        function calculateCash(input) {
            const amount = parseFloat(input.dataset.amount);
            const paid = parseFloat(input.value) || 0;
            const resultDiv = input.parentElement.querySelector('.cash-result');
            
            if (paid > 0) {
                if (paid >= amount) {
                    const change = paid - amount;
                    resultDiv.innerHTML = '<span class="text-success">Kembalian: Rp ' + change.toLocaleString('id-ID') + '</span>';
                } else {
                    const shortage = amount - paid;
                    resultDiv.innerHTML = '<span class="text-danger">Kekurangan: Rp ' + shortage.toLocaleString('id-ID') + '</span>';
                }
            } else {
                resultDiv.innerHTML = '';
            }
        }

        function generateConfirmationSummary() {
            // Baca langsung dari elemen DOM — tidak perlu fetch/AJAX.
            // Input di step lain memang di-disabled, tapi .value tetap tersimpan di DOM.
            const form = document.getElementById('ppdbForm');

            // Helper: ambil nilai dari input/select/textarea berdasarkan name,
            // termasuk yang sedang disabled
            function getFieldValue(fieldName) {
                const el = form.querySelector('[name="' + fieldName + '"]');
                if (!el) return '';
                return el.value || '';
            }

            let summary = '<div class="row">';

            // ── Data Murid ──────────────────────────────────────────────────
            const muridFields = Object.keys(formSettings).filter(
                key => formSettings[key] && formSettings[key].field_category === 'murid' && formSettings[key].is_active
            );
            if (muridFields.length > 0) {
                summary += '<div class="col-md-6 mb-4"><div class="card border-success"><div class="card-header bg-success text-white"><h6 class="mb-0"><i class="bi bi-person me-2"></i>Data Murid</h6></div><div class="card-body"><ul class="list-unstyled mb-0">';
                muridFields.forEach(fieldName => {
                    const fieldLabel = formSettings[fieldName].field_label;
                    const value = getFieldValue(fieldName) || '-';
                    summary += '<li class="mb-2"><strong>' + fieldLabel + ':</strong> ' + value + '</li>';
                });
                summary += '</ul></div></div></div>';
            }

            // ── Data Orang Tua ───────────────────────────────────────────────
            const ortuFields = Object.keys(formSettings).filter(
                key => formSettings[key] && formSettings[key].field_category === 'ortu' && formSettings[key].is_active
            );
            if (ortuFields.length > 0) {
                summary += '<div class="col-md-6 mb-4"><div class="card border-info"><div class="card-header bg-info text-white"><h6 class="mb-0"><i class="bi bi-people me-2"></i>Data Orang Tua</h6></div><div class="card-body"><ul class="list-unstyled mb-0">';
                const ayahFields = ortuFields.filter(f => f.includes('ayah'));
                const ibuFields  = ortuFields.filter(f => f.includes('ibu'));
                if (ayahFields.length > 0) {
                    summary += '<li class="mb-3"><strong class="text-primary">Data Ayah:</strong></li>';
                    ayahFields.forEach(fieldName => {
                        const fieldLabel = formSettings[fieldName].field_label;
                        const value = getFieldValue(fieldName) || '-';
                        summary += '<li class="mb-2 ps-3"><strong>' + fieldLabel + ':</strong> ' + value + '</li>';
                    });
                }
                if (ibuFields.length > 0) {
                    summary += '<li class="mb-3 mt-2"><strong class="text-primary">Data Ibu:</strong></li>';
                    ibuFields.forEach(fieldName => {
                        const fieldLabel = formSettings[fieldName].field_label;
                        const value = getFieldValue(fieldName) || '-';
                        summary += '<li class="mb-2 ps-3"><strong>' + fieldLabel + ':</strong> ' + value + '</li>';
                    });
                }
                summary += '</ul></div></div></div>';
            }

            // ── Data Wali ────────────────────────────────────────────────────
            const waliFields = Object.keys(formSettings).filter(
                key => formSettings[key] && formSettings[key].field_category === 'wali' && formSettings[key].is_active
            );
            const namaWali = getFieldValue('nama_wali');
            if (waliFields.length > 0 && namaWali) {
                summary += '<div class="col-md-6 mb-4"><div class="card border-warning"><div class="card-header bg-warning text-dark"><h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Wali</h6></div><div class="card-body"><ul class="list-unstyled mb-0">';
                waliFields.forEach(fieldName => {
                    const fieldLabel = formSettings[fieldName].field_label;
                    const value = getFieldValue(fieldName) || '-';
                    summary += '<li class="mb-2"><strong>' + fieldLabel + ':</strong> ' + value + '</li>';
                });
                summary += '</ul></div></div></div>';
            }

            // ── Dokumen ──────────────────────────────────────────────────────
            const dokumenFields = Object.keys(formSettings).filter(
                key => formSettings[key] && formSettings[key].field_category === 'dokumen' && formSettings[key].is_active
            );
            if (dokumenFields.length > 0) {
                const uploadedDocs = dokumenFields.filter(field => {
                    const el = form.querySelector('[name="' + field + '"]');
                    return el && el.files && el.files.length > 0;
                });
                if (uploadedDocs.length > 0) {
                    summary += '<div class="col-md-6 mb-4"><div class="card border-secondary"><div class="card-header bg-secondary text-white"><h6 class="mb-0"><i class="bi bi-file-earmark me-2"></i>Dokumen</h6></div><div class="card-body"><ul class="list-unstyled mb-0">';
                    uploadedDocs.forEach(field => {
                        const fieldLabel = formSettings[field].field_label;
                        summary += '<li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>' + fieldLabel + '</li>';
                    });
                    summary += '</ul></div></div></div>';
                }
            }

            summary += '</div>';
            document.getElementById('confirmationSummary').innerHTML = summary;
        }

        function validateNISN(input) {
            const value = input.value.replace(/\D/g, '');
            const alertDiv = document.getElementById('nisn-alert');
            
            // Clear previous alerts
            alertDiv.innerHTML = '';
            input.classList.remove('is-invalid', 'is-valid');
            
            if (value.length > 0 && value.length !== 10) {
                alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> NISN harus tepat 10 digit</span>';
                input.classList.add('is-invalid');
            } else if (value.length === 10) {
                // Check if NISN exists in database via AJAX
                fetch(`/ppdb/check-nisn?nisn=${value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> ' + data.message + '</span>';
                            input.classList.add('is-invalid');
                        } else {
                            alertDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> NISN valid dan tersedia</span>';
                            input.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking NISN:', error);
                        alertDiv.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Gagal memvalidasi NISN</span>';
                    });
            }
        }

        function validateNIK(input) {
            const value = input.value.replace(/\D/g, '');
            const alertDiv = document.getElementById('nik-alert');
            
            // Clear previous alerts
            alertDiv.innerHTML = '';
            input.classList.remove('is-invalid', 'is-valid');
            
            if (value.length > 0 && value.length !== 16) {
                alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> NIK harus tepat 16 digit</span>';
                input.classList.add('is-invalid');
            } else if (value.length === 16) {
                // Check if NIK exists in database via AJAX
                fetch(`/ppdb/check-nik?nik=${value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> ' + data.message + '</span>';
                            input.classList.add('is-invalid');
                        } else {
                            alertDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> NIK valid dan tersedia</span>';
                            input.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking NIK:', error);
                        alertDiv.innerHTML = '<span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Gagal memvalidasi NIK</span>';
                    });
            }
        }

       function previewFile(input, previewId, alertId) {
            const previewDiv = document.getElementById(previewId);
            const alertDiv = document.getElementById(alertId);
            const file = input.files[0];
            
            // Reset kondisi awal preview, alert, dan class validasi
            previewDiv.innerHTML = '';
            alertDiv.innerHTML = '';
            input.classList.remove('is-invalid', 'is-valid');
            
            if (!file) return;
            
            // Batasan diubah menjadi 2MB (2 * 1024 * 1024 bytes)
            const maxSize = 2 * 1024 * 1024; 
            if (file.size > maxSize) {
                // Tampilkan pesan alert teks merah tepat di bawah form upload
                alertDiv.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> Ukuran file melebihi 2MB (File Anda: ' + (file.size / (1024 * 1024)).toFixed(2) + 'MB). Anda harus mengubah file ini untuk melanjutkan.</span>';
                
                // Menambahkan class is-invalid agar input ditandai error
                input.classList.add('is-invalid'); 
                return;
            }
            
            // Jika lolos validasi ukuran (< 2MB)
            input.classList.add('is-valid');
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            alertDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Ukuran: ' + fileSizeMB + 'MB (Valid)</span>';
            
            // Render preview file (Gambar / PDF) sesuai bawaan kode Anda
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                previewDiv.innerHTML = '<div class="alert alert-info py-2"><i class="bi bi-file-earmark-pdf"></i> ' + file.name + '</div>';
            } else {
                previewDiv.innerHTML = '<div class="alert alert-info py-2"><i class="bi bi-file-earmark"></i> ' + file.name + '</div>';
            }
        }
        // Auto-save functionality with debouncing
        // Kirim hanya data teks (bukan file) — payload lebih kecil & cepat
        let autoSaveTimeout;
        const formSettings = JSON.parse(document.getElementById('ppdbForm').dataset.formSettings);

        function autoSaveForm() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                const form = document.getElementById('ppdbForm');

                // Kumpulkan hanya field teks/select/textarea — skip file input
                const textData = {};
                form.querySelectorAll('input:not([type="file"]):not([disabled]), select:not([disabled]), textarea:not([disabled])').forEach(el => {
                    if (el.name) textData[el.name] = el.value;
                });

                fetch('/ppdb/auto-save', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(textData),
                })
                .then(r => r.json())
                .catch(() => {}); // silent fail — jangan blokir UX
            }, 3000); // Debounce 3 detik — cukup untuk mengurangi jumlah request
        }

        // Load draft data on page load
        function loadDraftData() {
            fetch('/ppdb/get-draft')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const draft = data.data;
                        const form = document.getElementById('ppdbForm');
                        const skip = new Set(['id', 'session_id', 'ip_address', 'created_at', 'updated_at', 'dokumen_paths']);

                        Object.keys(draft).forEach(key => {
                            if (skip.has(key) || !draft[key]) return;
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) input.value = draft[key];
                        });
                    }
                })
                .catch(() => {});
        }

        // Add event listeners to all form inputs for auto-save
        function setupAutoSave() {
            const form = document.getElementById('ppdbForm');
            form.querySelectorAll('input:not([type="file"]), select, textarea').forEach(input => {
                input.addEventListener('input', autoSaveForm);
                input.addEventListener('change', autoSaveForm);
            });
        }

        // Re-enable semua input sebelum form disubmit agar data terkirim
        // Tambahkan loading overlay agar tidak bisa double-submit
        document.getElementById('ppdbForm').addEventListener('submit', function(e) {
            // Cek apakah ada file yang terlalu besar (is-invalid dari previewFile)
            const invalidFiles = this.querySelectorAll('input[type="file"].is-invalid');
            if (invalidFiles.length > 0) {
                e.preventDefault();
                invalidFiles[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Re-enable disabled fields agar ikut terkirim
            this.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
            });

            // Tampilkan loading dan cegah double submit
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';
            }
        });

        // Initialize - tampilkan step 1, dan disable input di semua step lainnya
        showStep(1);
        
        // Initialize NISN validation on page load
        const nisnInput = document.getElementById('nisn');
        if (nisnInput && nisnInput.value) {
            validateNISN(nisnInput);
        }
        
        // Initialize NIK validation on page load
        const nikInput = document.getElementById('nik');
        if (nikInput && nikInput.value) {
            validateNIK(nikInput);
        }
        
        // Setup auto-save functionality
        setupAutoSave();
        
        // Load draft data on page load
        loadDraftData();

        // ════════════════════════════════════════════════════════════════
        //  UPLOAD BUKTI PEMBAYARAN — QRIS / Transfer
        // ════════════════════════════════════════════════════════════════

        /**
         * Dipanggil saat user memilih file pada input bukti pembayaran.
         * Validasi realtime: hanya gambar, maks 2MB.
         * Jika valid → tampilkan preview.
         * Jika tidak valid → tampilkan pesan error, reset input.
         */
        function handleBuktiUpload(input) {
            const biayaId    = input.dataset.biayaId;
            const errorEl    = document.getElementById('bukti-error-'   + biayaId);
            const previewWrap= document.getElementById('bukti-preview-' + biayaId);
            const previewImg = document.getElementById('bukti-preview-img-' + biayaId);

            // Reset state sebelumnya
            errorEl.style.display    = 'none';
            errorEl.querySelector('span').textContent = '';
            previewWrap.style.display = 'none';
            previewImg.src = '';
            input.classList.remove('is-invalid', 'is-valid');

            const file = input.files[0];
            if (!file) return;

            // ── Validasi tipe ───────────────────────────────────────────
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                errorEl.querySelector('span').textContent =
                    'Format tidak didukung. Gunakan JPG, PNG, atau WEBP.';
                errorEl.style.display = 'block';
                input.classList.add('is-invalid');
                input.value = '';
                return;
            }

            // ── Validasi ukuran (maks 2MB) ──────────────────────────────
            const maxSize = 2 * 1024 * 1024;
            if (file.size > maxSize) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                errorEl.querySelector('span').textContent =
                    'Ukuran file ' + sizeMB + 'MB melebihi batas 2MB. Pilih file yang lebih kecil.';
                errorEl.style.display = 'block';
                input.classList.add('is-invalid');
                input.value = '';
                return;
            }

            // ── Lolos validasi → baca & tampilkan preview ───────────────
            input.classList.add('is-valid');
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src        = e.target.result;
                previewWrap.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        /**
         * Hapus pilihan file dan sembunyikan preview.
         */
        function hapusBukti(biayaId) {
            const input      = document.getElementById('bukti_' + biayaId);
            const errorEl    = document.getElementById('bukti-error-'   + biayaId);
            const previewWrap= document.getElementById('bukti-preview-' + biayaId);
            const previewImg = document.getElementById('bukti-preview-img-' + biayaId);

            input.value = '';
            input.classList.remove('is-invalid', 'is-valid');
            errorEl.style.display     = 'none';
            previewWrap.style.display = 'none';
            previewImg.src = '';
        }

        /**
         * Validasi sebelum lanjut ke step 6:
         * Setiap input bukti yang muncul (non-cash) harus sudah diisi.
         * Kembalikan true jika semua OK, false jika ada yang kosong/error.
         */
        function validateBuktiPembayaran() {
            const inputs  = document.querySelectorAll('#step5 .bukti-input');
            let allValid  = true;

            inputs.forEach(function (input) {
                const biayaId  = input.dataset.biayaId;
                const errorEl  = document.getElementById('bukti-error-' + biayaId);

                // Cek apakah sudah ada file terpilih dan tidak error
                if (!input.files || input.files.length === 0) {
                    errorEl.querySelector('span').textContent =
                        'Bukti pembayaran wajib diupload untuk metode QRIS / Transfer.';
                    errorEl.style.display = 'block';
                    input.classList.add('is-invalid');
                    allValid = false;
                } else if (input.classList.contains('is-invalid')) {
                    // Ada file tapi tidak lolos validasi sebelumnya (ukuran/tipe)
                    allValid = false;
                }
            });

            if (!allValid) {
                // Scroll ke input pertama yang bermasalah
                const firstInvalid = document.querySelector('#step5 .bukti-input.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return allValid;
        }
    </script>
</body>
</html>