{{--
    favicon.blade.php
    Letakkan @include('favicon') di dalam <head> setiap halaman.
    $sekolah sudah di-share secara global via AppServiceProvider → View::composer('*', ...).
--}}
@php
    $faviconUrl = isset($sekolah->logo) && $sekolah->logo
        ? \App\Helpers\ImageHelper::url($sekolah->logo)
        : asset('assets/img/default-favicon.png');
@endphp

{{-- Favicon standar --}}
<link rel="icon" type="image/png" href="{{ $faviconUrl }}">

{{-- Favicon untuk Apple & PWA (opsional tapi direkomendasikan) --}}
<link rel="apple-touch-icon" href="{{ $faviconUrl }}">
<link rel="shortcut icon" href="{{ $faviconUrl }}">
