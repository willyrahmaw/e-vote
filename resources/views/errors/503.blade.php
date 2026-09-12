@extends('errors.layout')

@section('title', '503 - Mode Pemeliharaan')
@section('code', '503')
@section('icon-bg', 'bg-blue-50 text-blue-600')
@section('icon-border', 'border-blue-200')
@section('badge-class', 'bg-blue-100 text-blue-800')
@section('badge-dot', 'bg-blue-500')

@section('icon')
    <i class="fa-solid fa-screwdriver-wrench"></i>
@endsection

@section('headline', 'Sistem Sedang Dalam Pemeliharaan')

@section('message', 'Sistem e-voting saat ini sedang menjalani proses pemeliharaan rutin atau peningkatan performa. Silakan periksa kembali beberapa saat lagi.')
