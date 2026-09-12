@extends('errors.layout')

@section('title', '500 - Kesalahan Server')
@section('code', '500')
@section('icon-bg', 'bg-rose-50 text-rose-600')
@section('icon-border', 'border-rose-200')
@section('badge-class', 'bg-rose-100 text-rose-800')
@section('badge-dot', 'bg-rose-500')

@section('icon')
    <i class="fa-solid fa-triangle-exclamation"></i>
@endsection

@section('headline', 'Terjadi Kesalahan Server')

@section('message', 'Terjadi kendala teknis internal pada server. Tim administrator telah menerima laporan pencatatan audit secara otomatis untuk penanganan lebih lanjut.')
