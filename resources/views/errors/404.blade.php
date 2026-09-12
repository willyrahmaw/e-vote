@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('code', '404')
@section('icon-bg', 'bg-blue-50 text-blue-600')
@section('icon-border', 'border-blue-200')
@section('badge-class', 'bg-blue-100 text-blue-800')
@section('badge-dot', 'bg-blue-500')

@section('icon')
    <i class="fa-solid fa-compass"></i>
@endsection

@section('headline', 'Halaman Tidak Ditemukan')

@section('message', 'Halaman yang Anda tuju tidak tersedia, telah dipindahkan, atau tautan yang Anda gunakan tidak valid.')
