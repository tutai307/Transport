@extends('layouts.app')
@section('title', 'Trang chủ')

@section('content')
<div class="text-center py-5">
    <i class="bi bi-truck text-primary" style="font-size: 64px;"></i>
    <h3 class="mt-3">Chào mừng đến Quản Lý Vận Chuyển</h3>
    <p class="text-muted">Chọn menu bên trái để bắt đầu.</p>
    <a href="{{ route('trips.create') }}" class="btn btn-primary btn-lg mt-3">
        <i class="bi bi-plus-circle"></i> Thêm chuyến xe mới
    </a>
</div>
@endsection
