@extends('layouts.app')
@section('title', 'Vật liệu')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-box-seam"></i> Danh sách vật liệu</h4>
    <a href="{{ route('materials.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm vật liệu
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Mã VL</th>
                <th>Tên vật liệu</th>
                <th>Đơn vị</th>
                <th>Giá nhập</th>
                <th>Giá bán</th>
                <th>Số chuyến</th>
                <th>Trạng thái</th>
                <th style="width:80px">Sửa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $i => $material)
                <tr class="{{ !$material->is_active ? 'table-secondary' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $material->code }}</span></td>
                    <td class="fw-bold">{{ $material->name }}</td>
                    <td>{{ $material->unit }}</td>
                    <td class="text-end">{{ number_format($material->import_price) }}</td>
                    <td class="text-end text-success fw-bold">{{ number_format($material->sell_price) }}</td>
                    <td>{{ $material->trips_count }}</td>
                    <td>
                        @if($material->is_active)
                            <span class="badge bg-success">Đang dùng</span>
                        @else
                            <span class="badge bg-secondary">Ngưng</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('materials.edit', $material) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
