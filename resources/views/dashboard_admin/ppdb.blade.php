<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($murid) ? 'Edit Data Murid' : 'Form PPDB | Tambah Murid' }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ asset($sekolah->logo) }}">
    @else
    <link rel="icon" type="image/png" href="{{ asset('assets/img/default-favicon.png') }}">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f7f6; }
        .wrapper { display: flex; width: 100%; }
        #content { width: 100%; padding: 20px 30px; }
        .card { border: none; border-radius: 15px; }
        .step-header { border-bottom: 2px solid #eee; margin-bottom: 25px; padding-bottom: 10px; }
        .btn-success { background-color: #198754; border: none; }
        .hidden { display: none; }
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
        .qris-image { max-width: 200px; height: auto; }
        input:focus, textarea:focus, select:focus { border-color: #198754 !important; outline: none !important; box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;}
    </style>
</head>
<body>
    <div class="wrapper">
        @include('dashboard_admin.sidebar_admin')
        <div id="content">
            <div class="container-fluid">
                <h4 class="fw-bold text-success mb-4">
                    {{ isset($murid) ? 'Edit Data Murid: ' . $murid->nama_lengkap : 'Pendaftaran Murid Baru (PPDB)' }}
                </h4>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ isset($murid) ? route('murid.update', $murid->id) : route('murid.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($murid)) @method('PUT') @endif

                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="indicator1" onclick="goToStep(1)">1</div>
                        <div class="step inactive" id="indicator2" onclick="goToStep(2)">2</div>
                        <div class="step inactive" id="indicator3" onclick="goToStep(3)">3</div>
                        <div class="step inactive" id="indicator4" onclick="goToStep(4)">4</div>
                        <div class="step inactive" id="indicator5" onclick="goToStep(5)">5</div>
                        <div class="step inactive" id="indicator6" onclick="goToStep(6)">6</div>
                    </div>

                    <!-- Step 1: Data Murid -->
                    <div id="step1" class="card p-4">
                        <h5 class="step-header text-success fw-bold"><i class="bi bi-1-circle-fill me-2"></i>Data Murid</h5>
                        <div class="row g-3">
                            @if(isset($formSettings['nama_lengkap']) && $formSettings['nama_lengkap']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $murid->nama_lengkap ?? '') }}" {{ $formSettings['nama_lengkap']->is_required ? 'required' : '' }}>
                            </div>
                            @endif
                            @if(isset($formSettings['jenis_kelamin']) && $formSettings['jenis_kelamin']->is_active)
                            <div class="col-md-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" {{ $formSettings['jenis_kelamin']->is_required ? 'required' : '' }}>
                                    <option value="">- Pilih -</option>
                                    <option value="laki-laki" {{ (old('jenis_kelamin', $murid->jenis_kelamin ?? '') == 'laki-laki') ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ (old('jenis_kelamin', $murid->jenis_kelamin ?? '') == 'perempuan') ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            @endif
                            @if(isset($formSettings['nisn']) && $formSettings['nisn']->is_active)
                            <div class="col-md-3">
                                <label class="form-label">NISN (10 Digit)</label>
                                <input type="text" name="nisn" id="nisn" class="form-control" maxlength="10" value="{{ old('nisn', $murid->nisn ?? '') }}" {{ $formSettings['nisn']->is_required ? 'required' : '' }} oninput="validateNISN(this)" data-murid-id="{{ $murid->id ?? '' }}">
                                <div id="nisn-alert" class="form-text mt-1"></div>
                            </div>
                            @endif
                            @if(isset($formSettings['nik']) && $formSettings['nik']->is_active)
                            <div class="col-md-4">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control" value="{{ old('nik', $murid->nik ?? '') }}" {{ $formSettings['nik']->is_required ? 'required' : '' }}>
                            </div>
                            @endif
                            @if(isset($formSettings['tempat_lahir']) && $formSettings['tempat_lahir']->is_active)
                            <div class="col-md-4">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $murid->tempat_lahir ?? '') }}" {{ $formSettings['tempat_lahir']->is_required ? 'required' : '' }}>
                            </div>
                            @endif
                            @if(isset($formSettings['tgl_lahir']) && $formSettings['tgl_lahir']->is_active)
                            <div class="col-md-4">
                                <label class="form-label">Tgl Lahir</label>
                                <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir', $murid->tgl_lahir ?? '') }}" {{ $formSettings['tgl_lahir']->is_required ? 'required' : '' }}>
                            </div>
                            @endif
                            @if(isset($formSettings['rt_rw']) && $formSettings['rt_rw']->is_active)
                            <div class="col-md-3"><label class="form-label">RT/RW</label><input type="text" name="rt_rw" class="form-control" value="{{ old('rt_rw', $murid->rt_rw ?? '') }}" {{ $formSettings['rt_rw']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['desa_kelurahan']) && $formSettings['desa_kelurahan']->is_active)
                            <div class="col-md-3"><label class="form-label">Desa/Kelurahan</label><input type="text" name="desa_kelurahan" class="form-control" value="{{ old('desa_kelurahan', $murid->desa_kelurahan ?? '') }}" {{ $formSettings['desa_kelurahan']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['kota_kabupaten']) && $formSettings['kota_kabupaten']->is_active)
                            <div class="col-md-3"><label class="form-label">Kota/Kabupaten</label><input type="text" name="kota_kabupaten" class="form-control" value="{{ old('kota_kabupaten', $murid->kota_kabupaten ?? '') }}" {{ $formSettings['kota_kabupaten']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['provinsi']) && $formSettings['provinsi']->is_active)
                            <div class="col-md-3"><label class="form-label">Provinsi</label><input type="text" name="provinsi" class="form-control" value="{{ old('provinsi', $murid->provinsi ?? '') }}" {{ $formSettings['provinsi']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['alamat_detail']) && $formSettings['alamat_detail']->is_active)
                            <div class="col-12"><label class="form-label">Alamat Detail</label><textarea name="alamat_detail" class="form-control" rows="2" {{ $formSettings['alamat_detail']->is_required ? 'required' : '' }}>{{ old('alamat_detail', $murid->alamat_detail ?? '') }}</textarea></div>
                            @endif
                            @if(isset($formSettings['transportasi']) && $formSettings['transportasi']->is_active)
                            <div class="col-md-4"><label class="form-label">Transportasi</label><input type="text" name="transportasi" class="form-control" value="{{ old('transportasi', $murid->transportasi ?? '') }}" {{ $formSettings['transportasi']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['no_hp']) && $formSettings['no_hp']->is_active)
                            <div class="col-md-4"><label class="form-label">No. HP</label><input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $murid->no_hp ?? '') }}" {{ $formSettings['no_hp']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['alamat_email']) && $formSettings['alamat_email']->is_active)
                            <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="alamat_email" class="form-control" value="{{ old('alamat_email', $murid->alamat_email ?? '') }}" {{ $formSettings['alamat_email']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['sekolah_asal']) && $formSettings['sekolah_asal']->is_active)
                            <div class="col-md-6"><label class="form-label">Sekolah Asal</label><input type="text" name="sekolah_asal" class="form-control" value="{{ old('sekolah_asal', $murid->sekolah_asal ?? '') }}" {{ $formSettings['sekolah_asal']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tinggi_badan']) && $formSettings['tinggi_badan']->is_active)
                            <div class="col-md-3"><label class="form-label">Tinggi (cm)</label><input type="number" step="0.1" name="tinggi_badan" class="form-control" value="{{ old('tinggi_badan', $murid->tinggi_badan ?? '') }}" {{ $formSettings['tinggi_badan']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['berat_badan']) && $formSettings['berat_badan']->is_active)
                            <div class="col-md-3"><label class="form-label">Berat (kg)</label><input type="number" step="0.1" name="berat_badan" class="form-control" value="{{ old('berat_badan', $murid->berat_badan ?? '') }}" {{ $formSettings['berat_badan']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['anak_ke']) && $formSettings['anak_ke']->is_active)
                            <div class="col-md-3"><label class="form-label">Anak Ke</label><input type="number" name="anak_ke" class="form-control" value="{{ old('anak_ke', $murid->anak_ke ?? '') }}" {{ $formSettings['anak_ke']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['jlm_saudara']) && $formSettings['jlm_saudara']->is_active)
                            <div class="col-md-3"><label class="form-label">Jml Saudara</label><input type="number" name="jlm_saudara" class="form-control" value="{{ old('jlm_saudara', $murid->jlm_saudara ?? '') }}" {{ $formSettings['jlm_saudara']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['jumlah_kakak']) && $formSettings['jumlah_kakak']->is_active)
                            <div class="col-md-3"><label class="form-label">Jml Kakak</label><input type="number" name="jumlah_kakak" class="form-control" value="{{ old('jumlah_kakak', $murid->jumlah_kakak ?? '') }}" {{ $formSettings['jumlah_kakak']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['jumlah_adik']) && $formSettings['jumlah_adik']->is_active)
                            <div class="col-md-3"><label class="form-label">Jml Adik</label><input type="number" name="jumlah_adik" class="form-control" value="{{ old('jumlah_adik', $murid->jumlah_adik ?? '') }}" {{ $formSettings['jumlah_adik']->is_required ? 'required' : '' }}></div>
                            @endif
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-success px-3 py-2" onclick="showStep2()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 2: Data Orang Tua -->
                    <div id="step2" class="card p-4 hidden">
                        <h5 class="step-header text-success fw-bold"><i class="bi bi-2-circle-fill me-2"></i>Data Orang Tua Murid</h5>
                        <div class="row g-3">
                            <h6 class="fw-bold text-muted">Data Ayah</h6>
                            @if(isset($formSettings['nama_ayah']) && $formSettings['nama_ayah']->is_active)
                            <div class="col-md-4"><label class="form-label">Nama Ayah</label><input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $murid->ortu->nama_ayah ?? '') }}" {{ $formSettings['nama_ayah']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tempat_lahir_ayah']) && $formSettings['tempat_lahir_ayah']->is_active)
                            <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir_ayah" class="form-control" value="{{ old('tempat_lahir_ayah', $murid->ortu->tempat_lahir_ayah ?? '') }}" {{ $formSettings['tempat_lahir_ayah']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tgl_lahir_ayah']) && $formSettings['tgl_lahir_ayah']->is_active)
                            <div class="col-md-4"><label class="form-label">Tgl Lahir</label><input type="date" name="tgl_lahir_ayah" class="form-control" value="{{ old('tgl_lahir_ayah', $murid->ortu->tgl_lahir_ayah ?? '') }}" {{ $formSettings['tgl_lahir_ayah']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['pendidikan_ayah']) && $formSettings['pendidikan_ayah']->is_active)
                            <div class="col-md-3"><label class="form-label">Pendidikan</label><input type="text" name="pendidikan_ayah" class="form-control" value="{{ old('pendidikan_ayah', $murid->ortu->pendidikan_ayah ?? '') }}" {{ $formSettings['pendidikan_ayah']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['pekerjaan_ayah']) && $formSettings['pekerjaan_ayah']->is_active)
                            <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $murid->ortu->pekerjaan_ayah ?? '') }}" {{ $formSettings['pekerjaan_ayah']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['penghasilan_ayah']) && $formSettings['penghasilan_ayah']->is_active)
                            <div class="col-md-3"><label class="form-label">Penghasilan</label><input type="number" name="penghasilan_ayah" class="form-control" value="{{ old('penghasilan_ayah', $murid->ortu->penghasilan_ayah ?? '') }}" {{ $formSettings['penghasilan_ayah']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['status_ayah']) && $formSettings['status_ayah']->is_active)
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status_ayah" class="form-select" {{ $formSettings['status_ayah']->is_required ? 'required' : '' }}>
                                    <option value="">- Pilih -</option>
                                    <option value="hidup" {{ (old('status_ayah', $murid->ortu->status_ayah ?? '') == 'hidup') ? 'selected' : '' }}>Hidup</option>
                                    <option value="meninggal" {{ (old('status_ayah', $murid->ortu->status_ayah ?? '') == 'meninggal') ? 'selected' : '' }}>Meninggal</option>
                                </select>
                            </div>
                            @endif
                            <hr>
                            <h6 class="fw-bold text-muted">Data Ibu</h6>
                            @if(isset($formSettings['nama_ibu']) && $formSettings['nama_ibu']->is_active)
                            <div class="col-md-4"><label class="form-label">Nama Ibu</label><input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $murid->ortu->nama_ibu ?? '') }}" {{ $formSettings['nama_ibu']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tempat_lahir_ibu']) && $formSettings['tempat_lahir_ibu']->is_active)
                            <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir_ibu" class="form-control" value="{{ old('tempat_lahir_ibu', $murid->ortu->tempat_lahir_ibu ?? '') }}" {{ $formSettings['tempat_lahir_ibu']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tgl_lahir_ibu']) && $formSettings['tgl_lahir_ibu']->is_active)
                            <div class="col-md-4"><label class="form-label">Tgl Lahir</label><input type="date" name="tgl_lahir_ibu" class="form-control" value="{{ old('tgl_lahir_ibu', $murid->ortu->tgl_lahir_ibu ?? '') }}" {{ $formSettings['tgl_lahir_ibu']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['pendidikan_ibu']) && $formSettings['pendidikan_ibu']->is_active)
                            <div class="col-md-3"><label class="form-label">Pendidikan</label><input type="text" name="pendidikan_ibu" class="form-control" value="{{ old('pendidikan_ibu', $murid->ortu->pendidikan_ibu ?? '') }}" {{ $formSettings['pendidikan_ibu']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['pekerjaan_ibu']) && $formSettings['pekerjaan_ibu']->is_active)
                            <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $murid->ortu->pekerjaan_ibu ?? '') }}" {{ $formSettings['pekerjaan_ibu']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['penghasilan_ibu']) && $formSettings['penghasilan_ibu']->is_active)
                            <div class="col-md-3"><label class="form-label">Penghasilan</label><input type="number" name="penghasilan_ibu" class="form-control" value="{{ old('penghasilan_ibu', $murid->ortu->penghasilan_ibu ?? '') }}" {{ $formSettings['penghasilan_ibu']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['status_ibu']) && $formSettings['status_ibu']->is_active)
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status_ibu" class="form-select" {{ $formSettings['status_ibu']->is_required ? 'required' : '' }}>
                                    <option value="">- Pilih -</option>
                                    <option value="hidup" {{ (old('status_ibu', $murid->ortu->status_ibu ?? '') == 'hidup') ? 'selected' : '' }}>Hidup</option>
                                    <option value="meninggal" {{ (old('status_ibu', $murid->ortu->status_ibu ?? '') == 'meninggal') ? 'selected' : '' }}>Meninggal</option>
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-3" onclick="showStep1()"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn btn-success px-3" onclick="showStep3()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
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
                            <div class="col-md-4"><label class="form-label">Nama Wali</label><input type="text" name="nama_wali" class="form-control" value="{{ old('nama_wali', $murid->wali->nama_wali ?? '') }}" {{ $formSettings['nama_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['hubungan_wali']) && $formSettings['hubungan_wali']->is_active)
                            <div class="col-md-4"><label class="form-label">Hubungan</label><input type="text" name="hubungan_wali" class="form-control" placeholder="Contoh: Kakek, Nenek, Paman" value="{{ old('hubungan_wali', $murid->wali->hubungan_wali ?? '') }}" {{ $formSettings['hubungan_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tempat_lahir_wali']) && $formSettings['tempat_lahir_wali']->is_active)
                            <div class="col-md-4"><label class="form-label">Tempat Lahir</label><input type="text" name="tempat_lahir_wali" class="form-control" value="{{ old('tempat_lahir_wali', $murid->wali->tempat_lahir_wali ?? '') }}" {{ $formSettings['tempat_lahir_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['tgl_lahir_wali']) && $formSettings['tgl_lahir_wali']->is_active)
                            <div class="col-md-4"><label class="form-label">Tgl Lahir</label><input type="date" name="tgl_lahir_wali" class="form-control" value="{{ old('tgl_lahir_wali', $murid->wali->tgl_lahir_wali ?? '') }}" {{ $formSettings['tgl_lahir_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['pendidikan_wali']) && $formSettings['pendidikan_wali']->is_active)
                            <div class="col-md-3"><label class="form-label">Pendidikan</label><input type="text" name="pendidikan_wali" class="form-control" value="{{ old('pendidikan_wali', $murid->wali->pendidikan_wali ?? '') }}" {{ $formSettings['pendidikan_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['pekerjaan_wali']) && $formSettings['pekerjaan_wali']->is_active)
                            <div class="col-md-3"><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_wali" class="form-control" value="{{ old('pekerjaan_wali', $murid->wali->pekerjaan_wali ?? '') }}" {{ $formSettings['pekerjaan_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['penghasilan_wali']) && $formSettings['penghasilan_wali']->is_active)
                            <div class="col-md-3"><label class="form-label">Penghasilan</label><input type="number" name="penghasilan_wali" class="form-control" value="{{ old('penghasilan_wali', $murid->wali->penghasilan_wali ?? '') }}" {{ $formSettings['penghasilan_wali']->is_required ? 'required' : '' }}></div>
                            @endif
                            @if(isset($formSettings['status_wali']) && $formSettings['status_wali']->is_active)
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status_wali" class="form-select" {{ $formSettings['status_wali']->is_required ? 'required' : '' }}>
                                    <option value="">- Pilih -</option>
                                    <option value="hidup" {{ (old('status_wali', $murid->wali->status_wali ?? '') == 'hidup') ? 'selected' : '' }}>Hidup</option>
                                    <option value="meninggal" {{ (old('status_wali', $murid->wali->status_wali ?? '') == 'meninggal') ? 'selected' : '' }}>Meninggal</option>
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="showStep2()"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn btn-success px-5" onclick="showStep4()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- Step 4: Dokumen Murid -->
                    <div id="step4" class="card p-4 hidden">
                        <h5 class="step-header text-success fw-bold"><i class="bi bi-4-circle-fill me-2"></i>Dokumen Murid (Opsional)</h5>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Upload dokumen bersifat opsional. Format yang diterima: JPG, PNG, PDF (Max 2MB).
                        </div>
                        <div class="row g-3">
                            @if(isset($formSettings['ktp_ayah']) && $formSettings['ktp_ayah']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">KTP Ayah</label>
                                <input type="file" name="ktp_ayah" id="ktp_ayah" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ktp_ayah']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ktp_ayah-preview', 'ktp_ayah-alert')">
                                <div id="ktp_ayah-alert" class="form-text mt-1"></div>
                                <div id="ktp_ayah-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->ktp_ayah)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['ktp_ibu']) && $formSettings['ktp_ibu']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">KTP Ibu</label>
                                <input type="file" name="ktp_ibu" id="ktp_ibu" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ktp_ibu']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ktp_ibu-preview', 'ktp_ibu-alert')">
                                <div id="ktp_ibu-alert" class="form-text mt-1"></div>
                                <div id="ktp_ibu-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->ktp_ibu)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['ktp_wali']) && $formSettings['ktp_wali']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">KTP Wali</label>
                                <input type="file" name="ktp_wali" id="ktp_wali" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ktp_wali']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ktp_wali-preview', 'ktp_wali-alert')">
                                <div id="ktp_wali-alert" class="form-text mt-1"></div>
                                <div id="ktp_wali-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->ktp_wali)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['kartu_keluarga']) && $formSettings['kartu_keluarga']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Kartu Keluarga</label>
                                <input type="file" name="kartu_keluarga" id="kartu_keluarga" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['kartu_keluarga']->is_required ? 'required' : '' }} onchange="previewFile(this, 'kartu_keluarga-preview', 'kartu_keluarga-alert')">
                                <div id="kartu_keluarga-alert" class="form-text mt-1"></div>
                                <div id="kartu_keluarga-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->kartu_keluarga)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['akte_kelahiran']) && $formSettings['akte_kelahiran']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Akte Kelahiran</label>
                                <input type="file" name="akte_kelahiran" id="akte_kelahiran" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['akte_kelahiran']->is_required ? 'required' : '' }} onchange="previewFile(this, 'akte_kelahiran-preview', 'akte_kelahiran-alert')">
                                <div id="akte_kelahiran-alert" class="form-text mt-1"></div>
                                <div id="akte_kelahiran-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->akte_kelahiran)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['ijazah_terakhir']) && $formSettings['ijazah_terakhir']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Ijazah Terakhir</label>
                                <input type="file" name="ijazah_terakhir" id="ijazah_terakhir" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['ijazah_terakhir']->is_required ? 'required' : '' }} onchange="previewFile(this, 'ijazah_terakhir-preview', 'ijazah_terakhir-alert')">
                                <div id="ijazah_terakhir-alert" class="form-text mt-1"></div>
                                <div id="ijazah_terakhir-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->ijazah_terakhir)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['transkip_nilai']) && $formSettings['transkip_nilai']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Transkip Nilai</label>
                                <input type="file" name="transkip_nilai" id="transkip_nilai" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['transkip_nilai']->is_required ? 'required' : '' }} onchange="previewFile(this, 'transkip_nilai-preview', 'transkip_nilai-alert')">
                                <div id="transkip_nilai-alert" class="form-text mt-1"></div>
                                <div id="transkip_nilai-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->transkip_nilai)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['surat_kelulusan']) && $formSettings['surat_kelulusan']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Surat Kelulusan</label>
                                <input type="file" name="surat_kelulusan" id="surat_kelulusan" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['surat_kelulusan']->is_required ? 'required' : '' }} onchange="previewFile(this, 'surat_kelulusan-preview', 'surat_kelulusan-alert')">
                                <div id="surat_kelulusan-alert" class="form-text mt-1"></div>
                                <div id="surat_kelulusan-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->surat_kelulusan)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['surat_keterangan_hasil_ujian']) && $formSettings['surat_keterangan_hasil_ujian']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Surat Keterangan Hasil Ujian</label>
                                <input type="file" name="surat_keterangan_hasil_ujian" id="surat_keterangan_hasil_ujian" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['surat_keterangan_hasil_ujian']->is_required ? 'required' : '' }} onchange="previewFile(this, 'surat_keterangan_hasil_ujian-preview', 'surat_keterangan_hasil_ujian-alert')">
                                <div id="surat_keterangan_hasil_ujian-alert" class="form-text mt-1"></div>
                                <div id="surat_keterangan_hasil_ujian-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->surat_keterangan_hasil_ujian)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['surat_pindahan']) && $formSettings['surat_pindahan']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Surat Pindahan</label>
                                <input type="file" name="surat_pindahan" id="surat_pindahan" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['surat_pindahan']->is_required ? 'required' : '' }} onchange="previewFile(this, 'surat_pindahan-preview', 'surat_pindahan-alert')">
                                <div id="surat_pindahan-alert" class="form-text mt-1"></div>
                                <div id="surat_pindahan-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->surat_pindahan)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                            @if(isset($formSettings['formulir_fisik']) && $formSettings['formulir_fisik']->is_active)
                            <div class="col-md-6">
                                <label class="form-label">Formulir Fisik</label>
                                <input type="file" name="formulir_fisik" id="formulir_fisik" class="form-control" accept=".jpg,.jpeg,.png,.pdf" {{ $formSettings['formulir_fisik']->is_required ? 'required' : '' }} onchange="previewFile(this, 'formulir_fisik-preview', 'formulir_fisik-alert')">
                                <div id="formulir_fisik-alert" class="form-text mt-1"></div>
                                <div id="formulir_fisik-preview" class="mt-2"></div>
                                @if($murid && $murid->dokumen && $murid->dokumen->formulir_fisik)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="showStep3()"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn btn-success px-5" onclick="showStep5()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
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
                                @if($biaya->is_active)
                                <div class="payment-card @if($biaya->account) @if($biaya->account->is_qris)qris_payment @else transfer_payment @endif @else cash_payment @endif">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $biaya->name }}</h6>
                                            <p class="text-success fw-bold mb-0">Rp {{ number_format($biaya->amount, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-end">
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
                                                <input type="number" class="form-control mt-2 cash-input" data-amount="{{ $biaya->amount }}" placeholder="Uang Diterima" oninput="calculateCash(this)">
                                                <div class="cash-result mt-2"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>Belum ada biaya yang diatur oleh admin.
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4" onclick="showStep4()"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="button" class="btn btn-success px-5" onclick="showStep6()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
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
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-3" onclick="showStep5()"><i class="bi bi-arrow-left"></i> Kembali</button>
                            <button type="submit" class="btn btn-success px-3">
                                {{ isset($murid) ? 'Simpan Perubahan' : 'Kirim Pendaftaran' }} <i class="bi bi-check-circle"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                } else {
                    stepEl.classList.add('hidden');
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

        function showStep2() { showStep(2); }
        function showStep3() { showStep(3); }
        function showStep4() { showStep(4); }
        function showStep5() { showStep(5); }
        function showStep6() { 
            generateConfirmationSummary();
            showStep(6); 
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
            const form = document.querySelector('form');
            const formData = new FormData(form);
            let summary = '<div class="row">';
            
            // Data Murid
            summary += '<div class="col-md-6 mb-3"><h6 class="fw-bold text-success">Data Murid</h6><ul class="list-unstyled">';
            summary += '<li><strong>Nama:</strong> ' + (formData.get('nama_lengkap') || '-') + '</li>';
            summary += '<li><strong>NISN:</strong> ' + (formData.get('nisn') || '-') + '</li>';
            summary += '<li><strong>NIK:</strong> ' + (formData.get('nik') || '-') + '</li>';
            summary += '<li><strong>Email:</strong> ' + (formData.get('alamat_email') || '-') + '</li>';
            summary += '</ul></div>';
            
            // Data Orang Tua
            summary += '<div class="col-md-6 mb-3"><h6 class="fw-bold text-success">Data Orang Tua</h6><ul class="list-unstyled">';
            summary += '<li><strong>Nama Ayah:</strong> ' + (formData.get('nama_ayah') || '-') + '</li>';
            summary += '<li><strong>Nama Ibu:</strong> ' + (formData.get('nama_ibu') || '-') + '</li>';
            summary += '</ul></div>';
            
            // Data Wali
            if (formData.get('nama_wali')) {
                summary += '<div class="col-md-6 mb-3"><h6 class="fw-bold text-success">Data Wali</h6><ul class="list-unstyled">';
                summary += '<li><strong>Nama Wali:</strong> ' + formData.get('nama_wali') + '</li>';
                summary += '<li><strong>Hubungan:</strong> ' + (formData.get('hubungan_wali') || '-') + '</li>';
                summary += '</ul></div>';
            }
            
            // Dokumen
            let hasDocuments = false;
            const documentFields = ['ktp_ayah', 'ktp_ibu', 'ktp_wali', 'kartu_keluarga', 'akte_kelahiran', 'ijazah_terakhir', 'transkip_nilai', 'surat_kelulusan', 'surat_keterangan_hasil_ujian', 'surat_pindahan', 'formulir_fisik'];
            documentFields.forEach(field => {
                if (formData.get(field)) {
                    hasDocuments = true;
                }
            });
            
            if (hasDocuments) {
                summary += '<div class="col-md-6 mb-3"><h6 class="fw-bold text-success">Dokumen</h6><ul class="list-unstyled">';
                documentFields.forEach(field => {
                    if (formData.get(field)) {
                        summary += '<li><i class="bi bi-check-circle text-success"></i> ' + field.replace(/_/g, ' ').toUpperCase() + '</li>';
                    }
                });
                summary += '</ul></div>';
            }
            
            summary += '</div>';
            document.getElementById('confirmationSummary').innerHTML = summary;
        }

        function validateNISN(input) {
            const value = input.value.replace(/\D/g, '');
            const alertDiv = document.getElementById('nisn-alert');
            const muridId = input.dataset.muridId || '';
            
            // Clear previous alerts
            alertDiv.innerHTML = '';
            input.classList.remove('is-invalid', 'is-valid');
            
            if (value.length > 0 && value.length !== 10) {
                alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> NISN harus tepat 10 digit</span>';
                input.classList.add('is-invalid');
            } else if (value.length === 10) {
                // Check if NISN exists in database via AJAX
                fetch(`/murid/check-nisn?nisn=${value}&exclude_id=${muridId}`)
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

        function previewFile(input, previewId, alertId) {
            const previewDiv = document.getElementById(previewId);
            const alertDiv = document.getElementById(alertId);
            const file = input.files[0];
            
            // Clear previous preview and alert
            previewDiv.innerHTML = '';
            alertDiv.innerHTML = '';
            
            if (!file) return;
            
            // Check file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
                alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> Ukuran file maksimal 5MB. File Anda: ' + (file.size / (1024 * 1024)).toFixed(2) + 'MB</span>';
                input.value = ''; // Clear the file input
                return;
            }
            
            // Show file info
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            alertDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Ukuran: ' + fileSizeMB + 'MB</span>';
            
            // Preview image if it's an image file
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

        // Initialize
        updateIndicators();
        
        // Initialize NISN validation on page load
        const nisnInput = document.getElementById('nisn');
        if (nisnInput && nisnInput.value) {
            validateNISN(nisnInput);
        }
    </script>
</body>
</html>