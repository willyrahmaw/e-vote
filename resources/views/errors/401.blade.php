@extends('errors.layout')

@section('title', '401 - Tidak Terautentikasi')
@section('code', '401')
@section('icon-bg', 'bg-amber-50 text-amber-600')
@section('icon-border', 'border-amber-200')
@section('badge-class', 'bg-amber-100 text-amber-800')
@section('badge-dot', 'bg-amber-500')

@section('icon')
    <i class="fa-solid fa-lock"></i>
@endsection

@section('headline', 'Autentikasi Diperlukan')

@section('message', 'Sesi Anda belum teridentifikasi atau telah berakhir. Silakan masuk terlebih dahulu untuk mengakses halaman ini.')
