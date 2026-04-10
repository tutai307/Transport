<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $modules = [
            [
                'title' => 'Quản lý Dự án',
                'description' => 'Danh mục các công trình đang triển khai',
                'icon' => 'bi-building',
                'route' => 'projects.index',
                'color' => 'primary'
            ],
            [
                'title' => 'Quản lý Xe',
                'description' => 'Danh sách xe và khối lượng mặc định',
                'icon' => 'bi-truck-front',
                'route' => 'vehicles.index',
                'color' => 'success'
            ],
            [
                'title' => 'Quản lý Tài xế',
                'description' => 'Thông tin lái xe và nhân viên',
                'icon' => 'bi-person-badge',
                'route' => 'employees.index',
                'color' => 'info'
            ],
            [
                'title' => 'Quản lý Vật liệu',
                'description' => 'Giá nhập và giá bán vật tư',
                'icon' => 'bi-box-seam',
                'route' => 'materials.index',
                'color' => 'warning'
            ],
            [
                'title' => 'Quản lý Tuyến đường',
                'description' => 'Các cung đường và điểm đi/đến',
                'icon' => 'bi-map',
                'route' => 'routes.index',
                'color' => 'danger'
            ],
        ];

        return view('settings.index', compact('modules'));
    }
}
