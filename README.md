# Transport Management System

Hệ thống quản lý vận chuyển hàng hóa xây dựng trên nền tảng Laravel 12, hỗ trợ theo dõi chuyến xe, doanh thu, lợi nhuận và xuất báo cáo Excel.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Database**: SQLite (mặc định), hỗ trợ MySQL/PostgreSQL
- **Build tool**: Vite 7
- **Auth**: Laravel Breeze
- **Export**: Maatwebsite Excel 3.1

## Tính năng chính

- **Quản lý chuyến xe**: Tạo, sửa, xóa chuyến với đầy đủ thông tin (dự án, xe, tài xế, vật tư, tuyến đường, giá cước)
- **Tính toán tự động**: Tổng cước, lợi nhuận tự động theo công thức `(giá bán - giá mua) × số lượng`
- **Báo cáo & thống kê**: Dashboard KPI, biểu đồ doanh thu 6 tháng, top dự án theo doanh thu
- **Lọc & xuất Excel**: Lọc báo cáo theo dự án, khoảng thời gian, xe; xuất file Excel động
- **Quản lý danh mục**: Dự án, Xe, Nhân viên, Vật tư, Tuyến đường
- **Responsive**: Tương thích mobile với Tailwind CSS

## Yêu cầu hệ thống

- PHP >= 8.2
- Composer
- Node.js >= 18 + npm
- SQLite (hoặc MySQL/PostgreSQL)

## Cài đặt

```bash
# Clone repository
git clone <repository-url>
cd transport

# Cài đặt toàn bộ (dependencies + migrate + seed)
composer setup
```

Hoặc cài đặt từng bước:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

## Chạy development

```bash
# Khởi động đồng thời Laravel server + queue + log + Vite
composer dev
```

Ứng dụng chạy tại `http://localhost:8000`.

Tài khoản mặc định sau khi seed:

| Email | Mật khẩu | Vai trò |
|---|---|---|
| admin@example.com | password | Admin |

## Cấu trúc thư mục

```
app/
├── Exports/           # Class xuất Excel
├── Http/
│   ├── Controllers/   # Logic nghiệp vụ
│   └── Requests/      # Validation
├── Models/            # Eloquent models
└── View/              # View components
database/
├── migrations/        # Schema database
└── seeders/           # Dữ liệu mẫu
resources/views/       # Blade templates
routes/
├── web.php            # Routes chính
└── auth.php           # Routes xác thực
```

## Các routes chính

| Route | Mô tả |
|---|---|
| `/dashboard` | Tổng quan KPI và biểu đồ |
| `/trips` | Danh sách chuyến theo dự án |
| `/trips/project/{project}/{year}/{month}` | Chi tiết chuyến theo tháng |
| `/reports` | Báo cáo có bộ lọc |
| `/reports/export` | Xuất Excel |
| `/projects`, `/vehicles`, `/employees` | Quản lý danh mục |
| `/materials`, `/routes` | Quản lý vật tư, tuyến đường |

## Database

Các bảng chính:

- `trips` — Chuyến xe (bảng trung tâm)
- `projects` — Dự án/công trình
- `vehicles` — Phương tiện vận chuyển
- `employees` — Tài xế/nhân viên
- `materials` — Vật tư/hàng hóa
- `routes` — Tuyến đường

## Lệnh hữu ích

```bash
composer test                         # Chạy test
php artisan pint                      # Format code PHP
php artisan migrate:fresh --seed      # Reset và seed lại database
npm run build                         # Build assets production
```

## Documentation

For developers and maintainers, refer to the docs directory:

- **[Project Overview & PDR](./docs/project-overview-pdr.md)** — Problem statement, users, features, success criteria
- **[Codebase Summary](./docs/codebase-summary.md)** — Directory structure, models, routes, patterns
- **[Code Standards](./docs/code-standards.md)** — Conventions, Eloquent patterns, Blade structure, testing
- **[System Architecture](./docs/system-architecture.md)** — Layered design, request flow, ER diagram, data flows
- **[Project Roadmap](./docs/project-roadmap.md)** — Migration history, known issues, Q3/Q4 priorities
- **[Deployment Guide](./docs/deployment-guide.md)** — Local setup, production config, web server setup, troubleshooting

All documentation is in English for clarity; UI remains in Vietnamese.

## Giấy phép

MIT
