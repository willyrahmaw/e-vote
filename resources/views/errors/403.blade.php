@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('icon-bg', 'bg-rose-50 text-rose-600')
@section('icon-border', 'border-rose-200')
@section('badge-class', 'bg-rose-100 text-rose-800')
@section('badge-dot', 'bg-rose-500')

@section('icon')
    <i class="fa-solid fa-ban"></i>
@endsection

@section('headline', 'Akses Ditolak')

@section('message', $exception->getMessage() ?: 'Anda tidak memiliki hak akses atau izin yang cukup untuk membuka halaman ini.')
