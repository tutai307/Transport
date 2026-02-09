@extends('layouts.app')
@section('title', 'Dự án')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><i class="bi bi-building"></i> Danh sách dự án</h4>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm dự án
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Tên dự án</th>
                <th>Mô tả</th>
                <th>Số chuyến</th>
                <th>Tạm tính lãi</th>
                <th>Trạng thái</th>
                <th style="width:80px">Sửa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $i => $project)
                <tr class="{{ !$project->is_active ? 'table-secondary' : '' }}">
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $project->name }}</td>
                    <td>{{ Str::limit($project->description, 50) }}</td>
                    <td>{{ $project->trips_count }}</td>
                    <td class="text-success fw-bold">{{ number_format($project->trips_sum_profit, 0, ',', '.') }} đ</td>
                    <td>
                        @if($project->is_active)
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Tạm ngưng</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
