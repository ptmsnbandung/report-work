@extends('layouts.app')

@section('title', 'Detail Tiket ' . $tiket->no_tiket)

{{-- ══════════════════════════════════════════════════════════════
     STYLES — Semua CSS spesifik halaman ini
     File: resources/views/tiket/partials/_styles.blade.php
══════════════════════════════════════════════════════════════ --}}
@push('styles')
@include('tiket.partials._styles')
@endpush

{{-- ══════════════════════════════════════════════════════════════
     CONTENT — Seluruh konten halaman
══════════════════════════════════════════════════════════════ --}}
@section('content')

{{-- Hero: breadcrumb, alert banners, header tiket, broadband card,
     summary metrics, SLA stepper, interval widget, closing checklist --}}
@include('tiket.partials._hero')

{{-- Tab Navigation + Tab Content (wrapper div.tab-content dibuka di dalam _tabs_nav) --}}
@include('tiket.partials._tabs_nav')

{{-- Tab Content Panels (masing-masing adalah div.tab-pane) --}}
@include('tiket.partials._tab_kronologis')
@include('tiket.partials._tab_resume')
@include('tiket.partials._tab_penanganan')
@include('tiket.partials._tab_material')
@include('tiket.partials._tab_dokumentasi')
@include('tiket.partials._tab_stopclock')

{{-- Semua Modal (dispatch, resume, material, JC, manuver, kronologis, closing, dll) --}}
@include('tiket.partials._modals')

@endsection

{{-- ══════════════════════════════════════════════════════════════
     SCRIPTS — Seluruh JavaScript halaman ini
     File: resources/views/tiket/partials/_scripts.blade.php
══════════════════════════════════════════════════════════════ --}}
@push('scripts')
@include('tiket.partials._scripts')
@endpush
