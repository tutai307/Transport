@extends('layouts.app')
@section('title', 'Tài xế')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-people"></i> Danh sách tài xế</h4>
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm tài xế
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Số chuyến</th>
                <th>Trạng thái</th>
                <th style="width:80px">Sửa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $i => $employee)
                <tr class="{{ !$employee->is_active ? 'table-secondary' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $employee->name }}</td>
                    <td>{{ $employee->phone ?? '—' }}</td>
                    <td>{{ $employee->trips_count }}</td>
                    <td>
                        @if($employee->is_active)
                            <span class="badge bg-success">Đang làm</span>
                        @else
                            <span class="badge bg-secondary">Nghỉ</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
