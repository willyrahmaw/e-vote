@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan')
@section('code', '429')
@section('icon-bg', 'bg-purple-50 text-purple-600')
@section('icon-border', 'border-purple-200')
@section('badge-class', 'bg-purple-100 text-purple-800')
@section('badge-dot', 'bg-purple-500')

@section('icon')
    <i class="fa-solid fa-gauge-high"></i>
@endsection

@section('headline', 'Batas Permintaan Terlampaui')

@section('message', 'Sistem mendeteksi terlalu banyak permintaan dari perangkat Anda dalam waktu singkat demi keamanan (Rate Limiting). Silakan tunggu beberapa saat.')
