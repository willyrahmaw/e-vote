@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa')
@section('code', '419')
@section('icon-bg', 'bg-amber-50 text-amber-600')
@section('icon-border', 'border-amber-200')
@section('badge-class', 'bg-amber-100 text-amber-800')
@section('badge-dot', 'bg-amber-500')

@section('icon')
    <i class="fa-solid fa-clock-rotate-left"></i>
@endsection

@section('headline', 'Sesi Telah Kedaluwarsa')

@section('message', 'Token keamanan CSRF Anda telah habis masa berlakunya karena tidak ada aktivitas. Silakan muat ulang halaman atau masuk kembali.')
