<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($murid) ? 'Edit Data Murid' : 'Form PPDB | Tambah Murid' }}</title>
    @if(isset($sekolah->logo))
    <link rel="icon" type="image/png" href="{{ \App\Helpers\ImageHelper::url($sekolah->logo) }}">
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
    @include('admin.sidebar')
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

                <form action="{{ isset($murid) ? route('murid.update', $murid->uuid) : route('murid.store') }}" method="POST" enctype="multipart/form-data" id="ppdbForm" data-form-settings="{{ json_encode($formSettings) }}">
                    @csrf
                    @if(isset($murid)) @method('PUT') @endif

                    <div class="step-indicator">
                    <div class="step active" id="indicator1" onclick="goToStep(1)">1</div>
                    <div class="step inactive" id="indicator2" onclick="goToStep(2)">2</div>
                    <div class="step inactive" id="indicator3" onclick="goToStep(3)">3</div>
                    <div class="step inactive" id="indicator4" onclick="goToStep(4)">4</div>

                    @if(isset($murid))
                        {{-- MODE EDIT: Cuma sampai angka 5, tapi kalau diklik mengarah ke step 6 (Konfirmasi) --}}
                        <div class="step inactive" id="indicator5" onclick="goToStep(6)">5</div>
                    @else
                        {{-- MODE TAMBAH (NORMAL): Ada step 5 (Biaya) dan step 6 (Konfirmasi) --}}
                        <div class="step inactive" id="indicator5" onclick="goToStep(5)">5</div>
                        <div class="step inactive" id="indicator6" onclick="goToStep(6)">6</div>
                    @endif
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
                            {{-- NIS Lama: tampil saat create; NIS Baru: tampil saat edit --}}
                            @if(!isset($murid))
                                @if(isset($formSettings['nis_lama']) && $formSettings['nis_lama']->is_active)
                                <div class="col-md-3">
                                    <label class="form-label">NIS Lama <small class="text-muted">(Sekolah Asal)</small></label>
                                    <input type="text" name="nis_lama" class="form-control" maxlength="20"
                                           value="{{ old('nis_lama') }}"
                                           {{ $formSettings['nis_lama']->is_required ? 'required' : '' }}
                                           placeholder="NIS dari sekolah asal">
                                </div>
                                @endif
                            @else
                               {{-- Mode edit: tampil nis_lama (readonly) dan nis_baru (editable) --}}
                                <div class="col-md-3">
                                    <label class="form-label">NIS Lama <small class="text-muted">(Sekolah Asal)</small></label>
                                    <input type="text" name="nis_lama" class="form-control" value="{{ $murid->nis_lama ?? '-' }}" readonly>
                                    <small class="text-muted">Tidak dapat diubah</small>
                                </div>
                                @if(isset($formSettings['nis_baru']) && $formSettings['nis_baru']->is_active)
                                <div class="col-md-3">
                                    <label class="form-label">NIS Baru <span class="text-danger">*</span> <small class="text-muted">(Diberikan Sekolah Ini)</small></label>
                                    <input type="text" name="nis_baru" id="nis_baru" class="form-control" maxlength="20"
                                           value="{{ old('nis_baru', $murid->nis_baru ?? '') }}"
                                           {{ $formSettings['nis_baru']->is_required ? 'required' : '' }}
                                           data-murid-id="{{ $murid->id ?? '' }}"
                                           oninput="validateNisBaru(this)"
                                           placeholder="NIS yang diberikan sekolah ini">
                                    <div id="nis_baru-alert" class="form-text mt-1"></div>
                                </div>
                                @endif
                            @endif
                            @if(isset($formSettings['nik']) && $formSettings['nik']->is_active)
                            <div class="col-md-4">
                                <label class="form-label">NIK (16 Digit)</label>
                                <input type="text" name="nik" id="nik" class="form-control" maxlength="16" value="{{ old('nik', $murid->nik ?? '') }}" {{ $formSettings['nik']->is_required ? 'required' : '' }} oninput="validateNIK(this)" data-murid-id="{{ $murid->id ?? '' }}">
                                <div id="nik-alert" class="form-text mt-1"></div>
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
                            @if(isset($murid))
                                <button type="button" class="btn btn-outline-success me-2 px-4" onclick="submitFromStep(1)">
                                    <i class="bi bi-save me-1"></i>Simpan
                                </button>
                            @endif
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
                            <div class="d-flex gap-2">
                                @if(isset($murid))
                                    <button type="button" class="btn btn-outline-success px-4" onclick="submitFromStep(2)">
                                        <i class="bi bi-save me-1"></i>Simpan
                                    </button>
                                @endif
                                <button type="button" class="btn btn-success px-3" onclick="showStep3()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                            </div>
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
                            <div class="d-flex gap-2">
                                @if(isset($murid))
                                    <button type="button" class="btn btn-outline-success px-4" onclick="submitFromStep(3)">
                                        <i class="bi bi-save me-1"></i>Simpan
                                    </button>
                                @endif
                                <button type="button" class="btn btn-success px-5" onclick="showStep4()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                            </div>
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
                                @if($murid && $murid->dokumen && $murid->dokumen->pasfoto)
                                    <small class="text-success"><i class="bi bi-check-circle"></i> Sudah ada</small>
                                @endif
                            </div>
                            @endif
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
                            <div class="d-flex gap-2">
                                @if(isset($murid))
                                    <button type="button" class="btn btn-outline-success px-4" onclick="submitFromStep(4)">
                                        <i class="bi bi-save me-1"></i>Simpan
                                    </button>
                                @endif
                                @if(isset($murid))
                                    <button type="button" class="btn btn-success px-5" onclick="showStep5()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                                @else
                                    <button type="button" class="btn btn-success px-5" onclick="showStep5()">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Biaya Pendaftaran — disembunyikan saat edit murid -->
                    @if(!isset($murid))
                    <div id="step5" class="card p-4 hidden">
                        <h5 class="step-header text-success fw-bold"><i class="bi bi-5-circle-fill me-2"></i>Biaya Pendaftaran</h5>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>Berikut adalah daftar biaya yang harus dibayar sesuai pengaturan sekolah.
                        </div>
                        @if($biayas->count() > 0)
                            @foreach($biayas as $biaya)
                                <div class="payment-card @if($biaya->account) @if($biaya->account->is_qris)qris_payment @else transfer_payment @endif @else cash_payment @endif @if(!$biaya->is_active)disabled @endif" @if(!$biaya->is_active)style="opacity: 0.6;"@endif>
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
                                                    <input type="number" class="form-control mt-2 cash-input" data-amount="{{ $biaya->amount }}" placeholder="Uang Diterima" oninput="calculateCash(this)">
                                                    <div class="cash-result mt-2"></div>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Dinonaktifkan</span>
                                                <small class="d-block text-muted">Pembayaran ini tidak tersedia saat ini</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
                    @endif {{-- !isset($murid) --}}

                    <!-- Step 6: Konfirmasi Pendaftaran -->
                    <div id="step6" class="card p-4 hidden">
                        <h5 class="step-header text-success fw-bold"><i class="bi bi-6-circle-fill me-2"></i>Konfirmasi Pendaftaran</h5>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Mohon periksa kembali seluruh data sebelum mengirim pendaftaran.
                        </div>
                        
                        <!-- Summary will be populated by JavaScript -->
                        <div id="confirmationSummary"></div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-3" onclick="{{ isset($murid) ? 'showStep4()' : 'showStep5()' }}"><i class="bi bi-arrow-left"></i> Kembali</button>
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
        // Saat mode edit murid: hanya 5 step (tanpa step biaya)
        const isEditMode = "{{ isset($murid) ? 'true' : 'false' }}" === 'true';
        let currentStep = 1;
        const totalSteps = isEditMode ? 5 : 6;

        // Saat edit, step 5 = konfirmasi (step 6 di mode tambah)
        // Remap agar step 5 edit = step 6 normal
        function realStepId(step) {
            if (isEditMode && step === 5) return 6;
            return step;
        }

        function updateIndicators() {
            // Daftar indicator yang ada di DOM
            const indicatorIds = isEditMode ? [1,2,3,4,5] : [1,2,3,4,5,6];
            // Di edit mode: indicator5 = step konfirmasi (DOM id "indicator5" mengarah ke step 6)
            indicatorIds.forEach((i, idx) => {
                const indicator = document.getElementById('indicator' + i);
                if (!indicator) return;
                const logicalStep = idx + 1; // posisi urutan tampilan
                indicator.classList.remove('active', 'completed', 'inactive');
                if (logicalStep < currentStep) {
                    indicator.classList.add('completed');
                } else if (logicalStep === currentStep) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.add('inactive');
                }
            });
        }

        function showStep(step) {
            // Buat list step ID yang benar-benar ada di DOM
            const allStepIds = isEditMode ? [1,2,3,4,6] : [1,2,3,4,5,6];

            allStepIds.forEach(i => {
                const stepEl = document.getElementById('step' + i);
                if (!stepEl) return;
                if (i === step) {
                    stepEl.classList.remove('hidden');
                    stepEl.querySelectorAll('input, select, textarea').forEach(el => {
                        el.disabled = false;
                    });
                } else {
                    stepEl.classList.add('hidden');
                    stepEl.querySelectorAll('input, select, textarea').forEach(el => {
                        el.disabled = true;
                    });
                }
            });
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

            // (Logika pemeriksaan field 'required' bawaan Anda tetap di sini...)
            stepEl.querySelectorAll('input[required]:not([disabled]), select[required]:not([disabled]), textarea[required]:not([disabled])').forEach(field => {
                const isEmpty = (field.type === 'file') ? (!field.files || field.files.length === 0) : (field.value.trim() === '');
                if (isEmpty) {
                    hasError = true;
                    field.style.borderColor = '#dc3545';
                    field.classList.add('is-invalid-required');
                    const labelEl = field.closest('.col-md-3, .col-md-4, .col-md-6, .col-12, .col')?.querySelector('label');
                    const labelText = labelEl ? labelEl.textContent.trim() : 'Field ini';
                    const alert = document.createElement('div');
                    alert.className = 'field-required-alert text-danger mt-1';
                    alert.style.fontSize = '0.8rem';
                    alert.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + labelText + ' wajib diisi.';
                    field.insertAdjacentElement('afterend', alert);
                    if (!firstInvalid) firstInvalid = field;
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
        function showStep5() {
            // Edit mode: step4 → langsung konfirmasi (step6), tidak ada step5
            if (isEditMode) {
                if (validateStep(4)) { generateConfirmationSummary(); showStep(6); }
            } else {
                if (validateStep(4)) showStep(5);
            }
        }
        function showStep6() {
            if (validateStep(isEditMode ? 4 : 5)) {
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
            // Baca langsung dari elemen DOM — .value tetap tersedia meski input di-disabled
            const form = document.getElementById('ppdbForm');

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
                
                // Tambahan: Pastikan NIS Lama ditangkap secara eksplisit
                if (document.querySelector('[name="nis_lama"]') && !muridFields.includes('nis_lama')) {
                    summary += '<li class="mb-2"><strong>NIS Lama:</strong> ' + (getFieldValue('nis_lama') || '-') + '</li>';
                }

                muridFields.forEach(fieldName => {
                    const fieldLabel = formSettings[fieldName].field_label;
                    const value = getFieldValue(fieldName) || '-';
                    summary += '<li class="mb-2"><strong>' + fieldLabel + ':</strong> ' + value + '</li>';
                });

                // Tambahan: Pastikan NIS Baru ditangkap secara eksplisit
                if (document.querySelector('[name="nis_baru"]') && !muridFields.includes('nis_baru')) {
                    summary += '<li class="mb-2"><strong>NIS Baru:</strong> ' + (getFieldValue('nis_baru') || '-') + '</li>';
                }

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

        function validateNIK(input) {
            const value = input.value.replace(/\D/g, '');
            const alertDiv = document.getElementById('nik-alert');
            const muridId = input.dataset.muridId || '';
            
            // Clear previous alerts
            alertDiv.innerHTML = '';
            input.classList.remove('is-invalid', 'is-valid');
            
            if (value.length > 0 && value.length !== 16) {
                alertDiv.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle"></i> NIK harus tepat 16 digit</span>';
                input.classList.add('is-invalid');
            } else if (value.length === 16) {
                // Check if NIK exists in database via AJAX
                fetch(`/murid/check-nik?nik=${value}&exclude_id=${muridId}`)
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
            
            // Reset kondisi awal preview, alert, dan class Bootstrap
            previewDiv.innerHTML = '';
            alertDiv.innerHTML = '';
            input.classList.remove('is-invalid', 'is-valid');
            
            if (!file) return;
            
            // Batasan diubah menjadi 2MB (2 * 1024 * 1024 bytes)
            const maxSize = 2 * 1024 * 1024; 
            if (file.size > maxSize) {
                // Tampilkan alert teks merah tepat di bawah input form
                alertDiv.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> Ukuran file melebihi 2MB (File Anda: ' + (file.size / (1024 * 1024)).toFixed(2) + 'MB). Anda harus mengganti file ini untuk melanjutkan.</span>';
                
                // Tambahkan class invalid agar terdeteksi gagal saat tombol Selanjutnya diklik
                input.classList.add('is-invalid'); 
                return;
            }
            
            // Jika lolos validasi ukuran (< 2MB)
            input.classList.add('is-valid');
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            alertDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle"></i> Ukuran: ' + fileSizeMB + 'MB (Valid)</span>';
            
            // Render preview file (Gambar / PDF)
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">';
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.innerHTML = '<div class="alert alert-info py-2"><i class="bi bi-file-earmark-pdf"></i> ' + file.name + '</div>';
            }
        }

        let autoSaveTimeout;
        const formSettings = JSON.parse(document.getElementById('ppdbForm').dataset.formSettings);

        function autoSaveForm() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                const form = document.getElementById('ppdbForm');
                const formData = new FormData(form);
                
                fetch('/murid/auto-save', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Data tersimpan otomatis');
                    }
                })
                .catch(error => {
                    console.error('Error auto-saving:', error);
                });
            }, 1000); // Debounce for 1 second
        }

        // Load draft data on page load
        function loadDraftData() {
            fetch('/murid/get-draft')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const draft = data.data;
                        const form = document.getElementById('ppdbForm');
                        
                        // Fill form fields with draft data
                        Object.keys(draft).forEach(key => {
                            if (key !== 'id' && key !== 'session_id' && key !== 'ip_address' && key !== 'created_at' && key !== 'updated_at' && key !== 'dokumen_paths') {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input && draft[key]) {
                                    input.value = draft[key];
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading draft data:', error);
                });
        }

        // Add event listeners to all form inputs for auto-save
        function setupAutoSave() {
            const form = document.getElementById('ppdbForm');
            const inputs = form.querySelectorAll('input:not([type="file"]), select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('input', autoSaveForm);
                input.addEventListener('change', autoSaveForm);
            });
        }

        // Re-enable semua input sebelum form disubmit agar data terkirim
        document.getElementById('ppdbForm').addEventListener('submit', function() {
            this.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
            });
        });

        /**
         * Simpan langsung dari step tertentu (mode edit).
         * Re-enable semua input → submit form tanpa harus ke step konfirmasi.
         */
        function submitFromStep(stepNum) {
            if (!validateStep(stepNum)) return; // validasi basic field required di step ini
            const form = document.getElementById('ppdbForm');
            // Re-enable semua input (termasuk step lain) agar semua data terkirim
            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = false;
            });
            form.submit();
        }

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
    </script>
</body>
</html>