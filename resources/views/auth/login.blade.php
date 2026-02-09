@extends('layouts.guest')

@section('content')
<div class="login-card">
    <div class="card shadow">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-truck text-primary" style="font-size: 48px;"></i>
                <h4 class="mt-2 fw-bold">Quản Lý Vận Chuyển</h4>
                <p class="text-muted">Đăng nhập để tiếp tục</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
