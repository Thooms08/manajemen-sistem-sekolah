@forelse($murids as $m)
<tr>
    <td>{{ $m->nisn }}</td>
    <td>{{ $m->nis_baru ?? '-' }}</td>
    <td class="fw-bold">{{ $m->nama_lengkap }}</td>
    <td>{{ $m->kelas->pluck('nama_kelas')->implode(', ') ?: '-' }}</td>
    <td>
        @if(isset($m->kelulusan) && $m->kelulusan->status == 'lulus')
            <span class="badge bg-success">Lulus</span>
        @elseif(isset($m->kelulusan) && $m->kelulusan->status == 'tidak lulus')
            <span class="badge bg-danger">Tidak Lulus</span>
        @else
            <span class="badge bg-secondary">Belum Diatur</span>
        @endif
    </td>
    <td class="text-center">
        @if(isset($m->kelulusan->ijazah))
            <a href="{{ route('murid.dokumen', ['path' => base64_encode($m->kelulusan->ijazah)]) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat Ijazah">
                <i class="bi bi-file-earmark-check-fill"></i>
            </a>
        @else
            <i class="bi bi-file-earmark-x text-muted" title="Belum ada file"></i>
        @endif
    </td>
    <td class="text-center">
        @if(isset($m->kelulusan->raport))
            <a href="{{ route('murid.dokumen', ['path' => base64_encode($m->kelulusan->raport)]) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="Lihat Raport">
                <i class="bi bi-file-earmark-text-fill"></i>
            </a>
        @else
            <i class="bi bi-file-earmark-x text-muted" title="Belum ada file"></i>
        @endif
    </td>
    <td class="text-center">
        <button class="btn btn-sm btn-outline-success" 
                title="Edit Status Kelulusan" 
                onclick="openEditKelulusan(this, {{ $m->id }})"
                data-ijazah="{{ isset($m->kelulusan->ijazah) ? route('murid.dokumen', ['path' => base64_encode($m->kelulusan->ijazah)]) : '' }}"
                data-raport="{{ isset($m->kelulusan->raport) ? route('murid.dokumen', ['path' => base64_encode($m->kelulusan->raport)]) : '' }}">
            <i class="bi bi-pencil-square"></i> Edit
        </button>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-4 text-muted">Data murid tidak ditemukan.</td>
</tr>
@endforelse