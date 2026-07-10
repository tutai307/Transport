@extends('layouts.app')
@section('title', 'Chấm công')

@section('content')

{{-- Tiêu đề trang --}}
<div class="page-header mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check"></i> Chấm công chuyến xe</h4>
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-mouse2"></i> Click = +1 &nbsp;|&nbsp; Chuột phải = −1
        </span>
    </div>
</div>

{{-- Bộ lọc --}}
<div class="card shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('attendance.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-5 col-md-3">
                    <label class="form-label mb-1 small fw-semibold">Ngày</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-control">
                </div>
                <div class="col-7 col-md-5">
                    <label class="form-label mb-1 small fw-semibold">Dự án</label>
                    <select name="project_id" class="form-select select2" data-placeholder="-- Chọn dự án --">
                        <option value=""></option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-table me-1"></i> Hiện bảng
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($projectId)

{{-- ════════════════════════════════════════════════ --}}
{{-- DESKTOP — Bảng ma trận (≥ md)                  --}}
{{-- ════════════════════════════════════════════════ --}}
<div class="card shadow-sm d-none d-md-block">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold">
            <i class="bi bi-grid-3x3 me-1"></i>
            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} &mdash;
            {{ $projects->firstWhere('id', $projectId)?->name }}
        </span>
        <button class="btn btn-success btn-sm px-3" id="btnNhapChuyen">
            <i class="bi bi-check-circle-fill me-1"></i> Nhập chuyến
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="font-size:14px;">
                <thead class="table-primary">
                    <tr>
                        <th class="align-middle" style="min-width:130px; position:sticky; left:0; z-index:2; background:#cfe2ff;">Biển số xe</th>
                        <th class="align-middle" style="min-width:110px;">Tài xế</th>
                        <th class="align-middle" style="min-width:160px;">Tiền cước/chuyến</th>
                        <th class="align-middle" style="min-width:210px;">Cung chặng</th>
                        @foreach($materials as $mat)
                            <th class="text-center align-middle" style="min-width:90px;">
                                <span title="{{ $mat->name }}" style="display:block;max-width:84px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $mat->name }}
                                </span>
                            </th>
                        @endforeach
                        <th class="text-center align-middle" style="min-width:68px;">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $rowIdx => $vehicle)
                        @php
                            $driver    = $vehicle->defaultDriver;
                            $hasDriver = !is_null($driver);
                            $cfg       = $vehicleConfigs[$vehicle->id] ?? ['freight_price' => 0, 'route_id' => null];
                        @endphp
                        <tr data-vehicle-id="{{ $vehicle->id }}"
                            data-driver-id="{{ $driver?->id ?? '' }}"
                            data-row-index="{{ $rowIdx }}">

                            <td class="fw-bold align-middle" style="position:sticky;left:0;z-index:1;background:var(--card-bg);">
                                {{ $vehicle->plate_number }}
                            </td>
                            <td class="align-middle">
                                {{ $hasDriver ? $driver->name : '—' }}
                            </td>
                            <td class="align-middle" style="padding:4px 8px;">
                                <div class="input-group input-group-sm">
                                    <input type="number"
                                        class="form-control form-control-sm freight-price-input"
                                        value="{{ $cfg['freight_price'] > 0 ? (int)$cfg['freight_price'] : '' }}"
                                        min="0" step="1000"
                                        data-vehicle-id="{{ $vehicle->id }}"
                                        data-row-index="{{ $rowIdx }}"
                                        data-auto-filled="{{ $rowIdx > 0 ? '1' : '0' }}"
                                        placeholder="0"
                                        {{ !$hasDriver ? 'disabled' : '' }}
                                        style="min-height:32px;font-size:13px;">
                                    <span class="input-group-text" style="font-size:12px;">đ</span>
                                </div>
                            </td>
                            <td class="align-middle" style="padding:4px 8px;">
                                <select class="form-select form-select-sm route-select"
                                    data-vehicle-id="{{ $vehicle->id }}"
                                    data-row-index="{{ $rowIdx }}"
                                    data-auto-filled="{{ $rowIdx > 0 ? '1' : '0' }}"
                                    {{ !$hasDriver ? 'disabled' : '' }}
                                    style="min-height:32px;font-size:13px;">
                                    <option value="">-- Chọn chặng --</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}"
                                            {{ (int)$cfg['route_id'] === $route->id ? 'selected' : '' }}>
                                            {{ $route->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            @foreach($materials as $mat)
                                @php $count = $marks[$vehicle->id . '_' . $mat->id] ?? 0; @endphp
                                <td class="tally-cell text-center p-1 align-middle"
                                    data-vehicle-id="{{ $vehicle->id }}"
                                    data-driver-id="{{ $driver?->id ?? '' }}"
                                    data-material-id="{{ $mat->id }}"
                                    data-count="{{ $count }}"
                                    @if(!$hasDriver) style="opacity:.3;pointer-events:none;" @endif
                                    title="{{ $mat->name }}: Click +1 | Chuột phải −1">
                                </td>
                            @endforeach
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-details"
                                    data-vehicle-id="{{ $vehicle->id }}"
                                    data-plate="{{ $vehicle->plate_number }}"
                                    data-driver="{{ $driver?->name ?? '—' }}"
                                    {{ !$hasDriver ? 'disabled' : '' }}>
                                    <i class="bi bi-clock-history"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════ --}}
{{-- MOBILE — Thẻ từng xe (< md)                    --}}
{{-- ════════════════════════════════════════════════ --}}
<div class="d-md-none" id="mobileCards">
    {{-- Thanh ngày / dự án + tổng --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="fw-bold text-muted small">
            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} &mdash;
            {{ $projects->firstWhere('id', $projectId)?->name }}
        </span>
        <span class="badge bg-primary" id="mTotalBadge">0 chuyến</span>
    </div>

    {{-- Nút mở popup chọn xe --}}
    <button type="button" class="btn btn-outline-primary w-100 mb-3 d-flex align-items-center justify-content-between"
        id="btnPickVehicle" data-bs-toggle="modal" data-bs-target="#vehiclePickerModal">
        <span>
            <i class="bi bi-truck-front me-2"></i>
            <span id="mSelectedLabel">{{ $vehicles->first()?->plate_number }}{{ $vehicles->first()?->defaultDriver ? ' — ' . $vehicles->first()->defaultDriver->name : '' }}</span>
        </span>
        <i class="bi bi-chevron-down text-muted"></i>
    </button>

    @foreach($vehicles as $rowIdx => $vehicle)
        @php
            $driver    = $vehicle->defaultDriver;
            $hasDriver = !is_null($driver);
            $cfg       = $vehicleConfigs[$vehicle->id] ?? ['freight_price' => 0, 'route_id' => null];
            $vehicleTotalMarks = 0;
            foreach($materials as $mat) {
                $vehicleTotalMarks += ($marks[$vehicle->id . '_' . $mat->id] ?? 0);
            }
        @endphp
        <div class="vc-card mb-3 {{ !$hasDriver ? 'vc-disabled' : '' }} {{ $rowIdx > 0 ? 'd-none' : '' }}"
            id="vcCard{{ $vehicle->id }}"
            data-vehicle-id="{{ $vehicle->id }}"
            data-row-index="{{ $rowIdx }}">

            {{-- Header xe --}}
            <div class="vc-head">
                <div class="vc-head-info">
                    <span class="vc-plate"><i class="bi bi-truck-front me-1"></i>{{ $vehicle->plate_number }}</span>
                    <span class="vc-driver">{{ $hasDriver ? $driver->name : 'Chưa có tài xế' }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="vc-count-badge" id="vcBadge{{ $vehicle->id }}">{{ $vehicleTotalMarks }} chuyến</span>
                    @if($hasDriver)
                    <button type="button" class="btn btn-sm btn-light btn-details"
                        data-vehicle-id="{{ $vehicle->id }}"
                        data-plate="{{ $vehicle->plate_number }}"
                        data-driver="{{ $driver->name }}"
                        title="Xem chi tiết">
                        <i class="bi bi-clock-history"></i>
                    </button>
                    @endif
                </div>
            </div>

            @if($hasDriver)
            {{-- Cấu hình hàng --}}
            <div class="vc-config">
                <div class="vc-config-item">
                    <label class="vc-label">Tiền cước</label>
                    <div class="input-group input-group-sm">
                        <input type="number"
                            class="form-control freight-price-input"
                            value="{{ $cfg['freight_price'] > 0 ? (int)$cfg['freight_price'] : '' }}"
                            min="0" step="1000"
                            data-vehicle-id="{{ $vehicle->id }}"
                            data-row-index="{{ $rowIdx }}"
                            data-auto-filled="{{ $rowIdx > 0 ? '1' : '0' }}"
                            placeholder="0">
                        <span class="input-group-text small">đ</span>
                    </div>
                </div>
                <div class="vc-config-item">
                    <label class="vc-label">Cung chặng</label>
                    <select class="form-select form-select-sm route-select"
                        data-vehicle-id="{{ $vehicle->id }}"
                        data-row-index="{{ $rowIdx }}"
                        data-auto-filled="{{ $rowIdx > 0 ? '1' : '0' }}">
                        <option value="">-- Chọn chặng --</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}"
                                {{ (int)$cfg['route_id'] === $route->id ? 'selected' : '' }}>
                                {{ $route->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Ô chấm theo vật liệu --}}
            <div class="vc-tiles">
                @foreach($materials as $mat)
                    @php $count = $marks[$vehicle->id . '_' . $mat->id] ?? 0; @endphp
                    <div class="m-tile"
                        data-vehicle-id="{{ $vehicle->id }}"
                        data-driver-id="{{ $driver->id }}"
                        data-material-id="{{ $mat->id }}"
                        data-count="{{ $count }}">
                        <div class="tile-name" title="{{ $mat->name }}">{{ $mat->name }}</div>
                        <div class="tile-tally"></div>
                        <div class="tile-controls">
                            <button type="button" class="tile-btn tile-minus">−</button>
                            <span class="tile-num">{{ $count }}</span>
                            <button type="button" class="tile-btn tile-plus">+</button>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
            <div class="text-center text-muted py-3 small fst-italic">Xe chưa được gán tài xế</div>
            @endif
        </div>
    @endforeach

    {{-- Padding để không bị che bởi nút cố định --}}
    <div style="height:80px;"></div>
</div>

{{-- Nút Nhập chuyến cố định (mobile) --}}
<div class="d-md-none position-fixed start-0 end-0" style="bottom:70px; z-index:600; padding:0 12px;">
    <button class="btn btn-success w-100 shadow-lg py-3" id="btnNhapChuyenMobile" style="border-radius:12px; font-size:16px;">
        <i class="bi bi-check-circle-fill me-2"></i>Nhập chuyến
    </button>
</div>

@else
{{-- Trạng thái trống --}}
<div class="text-center text-muted py-5">
    <i class="bi bi-calendar-event d-block fs-1 mb-3 opacity-25"></i>
    <p class="mb-0">Chọn ngày và dự án để hiện bảng chấm công.</p>
</div>
@endif

{{-- ── Modal chọn xe (mobile) ──────────────────────── --}}
<div class="modal fade" id="vehiclePickerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="margin-bottom:0; margin-top:auto; max-height:85vh;">
        <div class="modal-content" style="border-radius:16px 16px 0 0;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-truck-front me-2 text-primary"></i>Chọn xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="px-3 pt-2 pb-2">
                <input type="search" class="form-control" id="vehicleSearch"
                    placeholder="Tìm biển số hoặc tên tài xế…" autocomplete="off">
            </div>
            <div class="modal-body p-0">
                <div class="d-flex flex-wrap gap-2 p-3" id="vehiclePickerList">
                    @foreach($vehicles as $idx => $vehicle)
                        @php
                            $d     = $vehicle->defaultDriver;
                            $total = 0;
                            foreach($materials as $mat) $total += ($marks[$vehicle->id.'_'.$mat->id] ?? 0);
                        @endphp
                        <button type="button"
                            class="picker-vehicle-item btn btn-outline-secondary d-flex flex-column align-items-center"
                            data-vehicle-id="{{ $vehicle->id }}"
                            data-label="{{ $vehicle->plate_number }}{{ $d ? ' — ' . $d->name : '' }}"
                            data-search="{{ strtolower($vehicle->plate_number . ' ' . ($d?->name ?? '')) }}"
                            style="border-radius:99px; padding:6px 14px; min-width:0;">
                            <span class="fw-bold" style="font-size:13px; white-space:nowrap;">{{ $vehicle->plate_number }}</span>
                            <span class="text-muted" style="font-size:11px; white-space:nowrap;">
                                {{ $d?->name ?? '—' }}
                                @if($total > 0)<span class="text-primary fw-bold"> · {{ $total }}</span>@endif
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary w-100" id="btnShowAllFromModal">
                    <i class="bi bi-list-ul me-1"></i>Hiện tất cả xe
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal chi tiết ──────────────────────────────── --}}
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-clock-history me-1"></i>
                    <span id="detailTitle" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailBody"></div>
        </div>
    </div>
</div>

{{-- ── Modal xác nhận nhập chuyến ──────────────────── --}}
<div class="modal fade" id="commitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle-fill me-1"></i>Xác nhận nhập chuyến</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="commitBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success px-4" id="btnConfirmCommit">
                    <i class="bi bi-check-lg me-1"></i>Lưu tất cả chuyến xe
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ═══════════════════════════════════════════════
   DESKTOP — Ô tally
   ═══════════════════════════════════════════════ */
.tally-cell {
    height: 64px;
    cursor: pointer;
    user-select: none;
    transition: background .1s;
    min-width: 88px;
}
.tally-cell:hover  { background: rgba(13,110,253,.08); }
.tally-cell:active { background: rgba(13,110,253,.18); }
.tally-svg-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; gap:2px; color:#0d6efd; }
.tally-count { font-size:10px; font-weight:700; opacity:.7; }
.tally-empty { color:#ccc; font-size:1rem; }

/* ═══════════════════════════════════════════════
   MOBILE — Card xe
   ═══════════════════════════════════════════════ */
.vc-card {
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    background: var(--card-bg);
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
}
.vc-card.vc-disabled { opacity: .55; }

.vc-head {
    background: linear-gradient(135deg, #0d6efd, #0dcaf0);
    color: #fff;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.vc-head-info { display:flex; flex-direction:column; gap:1px; min-width:0; }
.vc-plate { font-weight:700; font-size:1rem; white-space:nowrap; }
.vc-driver { font-size:.82rem; opacity:.88; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.vc-count-badge {
    background: rgba(255,255,255,.22);
    color: #fff;
    font-size:.78rem;
    font-weight:700;
    padding: 3px 10px;
    border-radius: 99px;
    white-space: nowrap;
    flex-shrink: 0;
}

/* Cấu hình (tiền cước / cung chặng) */
.vc-config {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    padding: 10px 12px;
    border-bottom: 1px solid var(--border-color);
    background: var(--body-bg);
}
.vc-config-item {}
.vc-label { display:block; font-size:11px; font-weight:600; color:var(--text-color); opacity:.65; margin-bottom:3px; text-transform:uppercase; letter-spacing:.4px; }
.vc-config .form-control,
.vc-config .form-select { min-height: 38px !important; font-size: 14px !important; }

/* Ô vật liệu */
.vc-tiles {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 8px;
    padding: 10px 12px 12px;
}

.m-tile {
    border: 1.5px solid var(--border-color);
    border-radius: 8px;
    padding: 8px 6px;
    text-align: center;
    background: var(--body-bg);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.tile-name {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-color);
    opacity: .75;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: .3px;
}

.tile-tally {
    min-height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0d6efd;
    width: 100%;
}

.tile-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
}

.tile-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 2px solid;
    background: transparent;
    font-size: 1.3rem;
    font-weight: 700;
    line-height: 1;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    touch-action: manipulation;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .1s, color .1s;
}
.tile-minus { border-color: #dc3545; color: #dc3545; }
.tile-minus:active, .tile-minus:focus { background: #dc3545; color: #fff; outline: none; }
.tile-plus  { border-color: #0d6efd; color: #0d6efd; }
.tile-plus:active, .tile-plus:focus  { background: #0d6efd; color: #fff; outline: none; }

.tile-num {
    font-size: 1.3rem;
    font-weight: 800;
    min-width: 28px;
    color: var(--text-color);
}

/* ── Picker xe: bottom sheet style ── */
#vehiclePickerModal .modal-dialog {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    margin: 0;
    max-width: 100%;
    max-height: 88vh;
}
#vehiclePickerModal .modal-content {
    border-radius: 16px 16px 0 0;
    max-height: 88vh;
}
#vehiclePickerModal .modal-body {
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
.vc-hidden { display: none !important; }
.picker-vehicle-item:active,
.picker-vehicle-item.selected {
    background: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #fff !important;
}
.picker-vehicle-item.selected .text-muted,
.picker-vehicle-item.selected .text-primary { color: rgba(255,255,255,.8) !important; }

/* Tile sáng lên khi có chuyến */
.m-tile.has-count {
    border-color: #0d6efd;
    background: rgba(13,110,253,.06);
}
.m-tile.has-count .tile-name { opacity: 1; color: #0d6efd; }

</style>
@endpush

@push('scripts')
<script>
// ════════════════════════════════════════════════════════
// CẤU HÌNH
// ════════════════════════════════════════════════════════
const CC = {
    date:      '{{ $date }}',
    projectId: {{ $projectId ?: 'null' }},
    csrf:      document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
};

// ── State tập trung ────────────────────────────────────
// [vehicleId] => { freight_price, route_id }
const vState = {
    @foreach($vehicles as $vehicle)
    @php $cfg = $vehicleConfigs[$vehicle->id] ?? ['freight_price' => 0, 'route_id' => null]; @endphp
    {{ $vehicle->id }}: { freight_price: {{ (float)$cfg['freight_price'] }}, route_id: {{ $cfg['route_id'] ?: 'null' }} },
    @endforeach
};

// ════════════════════════════════════════════════════════
// VẼ TALLY SVG
// ════════════════════════════════════════════════════════
function makeTallySVG(count, compact = false) {
    if (!count) return compact
        ? '<i class="bi bi-dash text-muted" style="font-size:.9rem;"></i>'
        : '<div class="tally-empty"><i class="bi bi-dash"></i></div>';

    const barH = compact ? 18 : 26, barW = 2, gapBar = compact ? 6 : 8, gapGroup = compact ? 10 : 14, pad = 5;
    const groups = Math.floor(count / 5), rem = count % 5;
    let paths = [], cx = pad;

    for (let g = 0; g < groups; g++) {
        for (let i = 0; i < 4; i++) {
            const bx = cx + i * gapBar;
            paths.push(`<line x1="${bx}" y1="3" x2="${bx}" y2="${3+barH}" stroke="currentColor" stroke-width="${barW}" stroke-linecap="round"/>`);
        }
        paths.push(`<line x1="${cx-3}" y1="${3+barH}" x2="${cx+3*gapBar+3}" y2="3" stroke="currentColor" stroke-width="${barW}" stroke-linecap="round"/>`);
        cx += 3 * gapBar + gapGroup;
    }
    for (let i = 0; i < rem; i++) {
        const bx = cx + i * gapBar;
        paths.push(`<line x1="${bx}" y1="3" x2="${bx}" y2="${3+barH}" stroke="currentColor" stroke-width="${barW}" stroke-linecap="round"/>`);
    }

    const w = Math.max(36, cx + Math.max(0, rem - 1) * gapBar + pad + 4);
    const h = barH + 6;

    if (compact) {
        return `<svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}">${paths.join('')}</svg>`;
    }
    return `<div class="tally-svg-wrap">
        <svg width="${w}" height="${h}" viewBox="0 0 ${w} ${h}">${paths.join('')}</svg>
        <div class="tally-count">${count} chuyến</div>
    </div>`;
}

// ════════════════════════════════════════════════════════
// CẬP NHẬT HIỂN THỊ SAU KHI MARK/UNMARK
// ════════════════════════════════════════════════════════
function updateAllCells(vehicleId, materialId, count) {
    // Desktop tally cell
    const desktopCell = document.querySelector(
        `.tally-cell[data-vehicle-id="${vehicleId}"][data-material-id="${materialId}"]`
    );
    if (desktopCell) {
        desktopCell.dataset.count = count;
        desktopCell.innerHTML = makeTallySVG(count, false);
    }

    // Mobile tile
    const mobileTile = document.querySelector(
        `.m-tile[data-vehicle-id="${vehicleId}"][data-material-id="${materialId}"]`
    );
    if (mobileTile) {
        mobileTile.dataset.count = count;
        mobileTile.querySelector('.tile-tally').innerHTML = makeTallySVG(count, true);
        mobileTile.querySelector('.tile-num').textContent = count;
        mobileTile.classList.toggle('has-count', count > 0);
    }

    // Cập nhật badge tổng xe (mobile)
    updateVehicleBadge(vehicleId);
    updateGlobalBadge();
}

function updateVehicleBadge(vehicleId) {
    let total = 0;
    document.querySelectorAll(`.m-tile[data-vehicle-id="${vehicleId}"]`).forEach(t => {
        total += +t.dataset.count;
    });
    const badge = document.getElementById(`vcBadge${vehicleId}`);
    if (badge) badge.textContent = `${total} chuyến`;
}

function updateGlobalBadge() {
    let total = 0;
    document.querySelectorAll('.m-tile').forEach(t => total += +t.dataset.count);
    const el = document.getElementById('mTotalBadge');
    if (el) el.textContent = `${total} chuyến`;
}

// ════════════════════════════════════════════════════════
// API
// ════════════════════════════════════════════════════════
async function apiFetch(url, method = 'GET', body = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CC.csrf } };
    if (body) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Lỗi máy chủ');
    return json;
}

// ════════════════════════════════════════════════════════
// CHẤM CÔNG
// ════════════════════════════════════════════════════════
async function doMark(vehicleId, driverId, materialId) {
    if (!driverId) return;
    const cfg = vState[vehicleId] || { freight_price: 0, route_id: null };
    try {
        const res = await apiFetch('/api/attendance/mark', 'POST', {
            attendance_date: CC.date,
            project_id:      CC.projectId,
            vehicle_id:      vehicleId,
            driver_id:       driverId,
            material_id:     materialId,
            route_id:        cfg.route_id,
            freight_price:   cfg.freight_price,
        });
        updateAllCells(vehicleId, materialId, res.count);
    } catch (e) { toast(e.message, 'danger'); }
}

async function doUnmark(vehicleId, materialId) {
    const tile = document.querySelector(`.m-tile[data-vehicle-id="${vehicleId}"][data-material-id="${materialId}"]`)
        || document.querySelector(`.tally-cell[data-vehicle-id="${vehicleId}"][data-material-id="${materialId}"]`);
    if (+tile?.dataset.count <= 0) return;
    try {
        const res = await apiFetch('/api/attendance/unmark', 'POST', {
            attendance_date: CC.date,
            project_id:      CC.projectId,
            vehicle_id:      vehicleId,
            material_id:     materialId,
        });
        updateAllCells(vehicleId, materialId, res.count);
    } catch (e) { toast(e.message, 'danger'); }
}

// ════════════════════════════════════════════════════════
// CONFIG (tiền cước / cung chặng)
// ════════════════════════════════════════════════════════
const configTimers = {};

function syncInputs(vehicleId) {
    const cfg = vState[vehicleId] || { freight_price: 0, route_id: null };
    document.querySelectorAll(`.freight-price-input[data-vehicle-id="${vehicleId}"]`).forEach(inp => {
        if (document.activeElement !== inp) inp.value = cfg.freight_price || '';
    });
    document.querySelectorAll(`.route-select[data-vehicle-id="${vehicleId}"]`).forEach(sel => {
        if (document.activeElement !== sel) sel.value = cfg.route_id || '';
    });
}

function scheduleConfigUpdate(vehicleId) {
    clearTimeout(configTimers[vehicleId]);
    configTimers[vehicleId] = setTimeout(async () => {
        const cfg = vState[vehicleId] || {};
        try {
            await apiFetch('/api/attendance/config', 'PATCH', {
                attendance_date: CC.date,
                project_id:      CC.projectId,
                vehicle_id:      vehicleId,
                freight_price:   cfg.freight_price || 0,
                route_id:        cfg.route_id || null,
            });
        } catch (_) {}
    }, 700);
}

function applyAutoFill(rowIdx, sourceVehicleId) {
    if (rowIdx !== 0) return;
    const src = vState[sourceVehicleId] || {};
    document.querySelectorAll('.freight-price-input[data-auto-filled="1"]').forEach(inp => {
        const vid = inp.dataset.vehicleId;
        if (!vState[vid]) vState[vid] = {};
        vState[vid].freight_price = src.freight_price;
        syncInputs(vid);
        scheduleConfigUpdate(vid);
    });
    document.querySelectorAll('.route-select[data-auto-filled="1"]').forEach(sel => {
        const vid = sel.dataset.vehicleId;
        if (!vState[vid]) vState[vid] = {};
        vState[vid].route_id = src.route_id;
        syncInputs(vid);
        scheduleConfigUpdate(vid);
    });
}

// ════════════════════════════════════════════════════════
// MODAL CHI TIẾT
// ════════════════════════════════════════════════════════
async function showDetails(vehicleId, plate, driverName) {
    document.getElementById('detailTitle').textContent = `${plate} — ${driverName}`;
    document.getElementById('detailBody').innerHTML = spinnerHTML();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('detailsModal')).show();

    try {
        const list = await apiFetch(`/api/attendance/details?date=${CC.date}&project_id=${CC.projectId}&vehicle_id=${vehicleId}`);

        if (!list.length) {
            document.getElementById('detailBody').innerHTML =
                '<p class="text-center text-muted py-4"><i class="bi bi-inbox d-block fs-2 mb-2"></i>Chưa có chuyến nào được chấm hôm nay.</p>';
            return;
        }

        const rows = list.map((m, i) => `
            <tr>
                <td class="text-center text-muted">${i + 1}</td>
                <td><strong>${m.material}</strong></td>
                <td class="text-muted small">${m.route}</td>
                <td class="text-end">${m.freight_price}</td>
                <td class="text-center"><span class="badge bg-secondary">${m.marked_at}</span></td>
            </tr>`).join('');

        document.getElementById('detailBody').innerHTML = `
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-2">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th><th>Vật liệu</th><th>Cung chặng</th>
                            <th class="text-end">Tiền cước</th><th class="text-center">Giờ chấm</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
            <p class="text-end text-muted small mb-0">Tổng: <strong class="text-primary">${list.length} chuyến</strong></p>`;
    } catch (e) {
        document.getElementById('detailBody').innerHTML = `<p class="text-danger p-3 mb-0"><i class="bi bi-exclamation-circle"></i> ${e.message}</p>`;
        window.showToast(e.message, 'error');
    }
}

// ════════════════════════════════════════════════════════
// MODAL NHẬP CHUYẾN
// ════════════════════════════════════════════════════════
async function showCommitModal() {
    document.getElementById('commitBody').innerHTML = spinnerHTML('text-success');
    document.getElementById('btnConfirmCommit').disabled = false;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('commitModal')).show();

    try {
        const data = await apiFetch(`/api/attendance/pending?date=${CC.date}&project_id=${CC.projectId}`);

        if (!data.rows.length) {
            document.getElementById('commitBody').innerHTML =
                '<p class="text-center text-muted py-4"><i class="bi bi-inbox d-block fs-2 mb-2"></i>Chưa có chuyến nào được chấm công.</p>';
            document.getElementById('btnConfirmCommit').disabled = true;
            return;
        }

        const fmt = v => new Intl.NumberFormat('vi-VN').format(v);
        const grandTotal = data.rows.reduce((s, r) => s + r.total, 0);

        const rows = data.rows.map((r, i) => `
            <tr>
                <td class="text-center text-muted">${i + 1}</td>
                <td><strong>${r.vehicle}</strong></td>
                <td>${r.driver}</td>
                <td>${r.material}</td>
                <td class="text-muted small">${r.route}</td>
                <td class="text-end">${fmt(r.freight_price)} đ</td>
                <td class="text-center fw-bold text-primary">${r.quantity}</td>
                <td class="text-end fw-bold text-success">${fmt(r.total)} đ</td>
            </tr>`).join('');

        document.getElementById('commitBody').innerHTML = `
            <div class="alert alert-info border-0 py-2 mb-3">
                <i class="bi bi-info-circle me-1"></i>
                <strong>${data.total_trips}</strong> chuyến sẽ được lưu vào danh sách chuyến xe ngày
                <strong>${CC.date.split('-').reverse().join('/')}</strong>.
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Xe</th><th>Tài xế</th><th>Vật liệu</th><th>Cung chặng</th>
                            <th class="text-end">Tiền cước</th>
                            <th class="text-center">Số chuyến</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                    <tfoot class="fw-bold table-light">
                        <tr>
                            <td colspan="6" class="text-end">Tổng cộng:</td>
                            <td class="text-center text-primary">${data.total_trips}</td>
                            <td class="text-end text-success">${fmt(grandTotal)} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>`;
    } catch (e) {
        document.getElementById('commitBody').innerHTML = `<p class="text-danger p-3 mb-0"><i class="bi bi-exclamation-circle"></i> ${e.message}</p>`;
        document.getElementById('btnConfirmCommit').disabled = true;
        window.showToast(e.message, 'error');
    }
}

async function commitTrips() {
    const btn = document.getElementById('btnConfirmCommit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang lưu...';
    try {
        const res = await apiFetch('/attendance/commit', 'POST', { date: CC.date, project_id: CC.projectId });
        bootstrap.Modal.getInstance(document.getElementById('commitModal')).hide();

        // Reset tất cả ô về 0
        document.querySelectorAll('.tally-cell, .m-tile').forEach(el => {
            el.dataset.count = 0;
        });
        document.querySelectorAll('.tally-cell').forEach(el => {
            el.innerHTML = makeTallySVG(0, false);
        });
        document.querySelectorAll('.m-tile').forEach(el => {
            el.querySelector('.tile-tally').innerHTML = makeTallySVG(0, true);
            el.querySelector('.tile-num').textContent = '0';
            el.classList.remove('has-count');
        });
        document.querySelectorAll('.vc-count-badge').forEach(b => b.textContent = '0 chuyến');
        const gb = document.getElementById('mTotalBadge');
        if (gb) gb.textContent = '0 chuyến';

        toast(res.message, 'success');
    } catch (e) {
        toast(e.message, 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Lưu tất cả chuyến xe';
    }
}

// ════════════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════════════
function spinnerHTML(cls = 'text-primary') {
    return `<div class="text-center py-4"><div class="spinner-border ${cls}" role="status"></div></div>`;
}

function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `alert alert-${type} alert-dismissible position-fixed shadow-lg`;
    el.style.cssText = 'top:16px;right:16px;z-index:9999;min-width:260px;max-width:400px;';
    el.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-1"></i>${msg}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    document.body.appendChild(el);
    setTimeout(() => el?.remove(), 5000);
}

// ════════════════════════════════════════════════════════
// KHỞI TẠO
// ════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    // ── Khởi tạo desktop cells ──
    document.querySelectorAll('.tally-cell').forEach(cell => {
        cell.innerHTML = makeTallySVG(+cell.dataset.count, false);

        cell.addEventListener('click', () => {
            doMark(cell.dataset.vehicleId, cell.dataset.driverId, cell.dataset.materialId);
        });
        cell.addEventListener('contextmenu', e => {
            e.preventDefault();
            doUnmark(cell.dataset.vehicleId, cell.dataset.materialId);
        });
    });

    // ── Khởi tạo mobile tiles ──
    document.querySelectorAll('.m-tile').forEach(tile => {
        const count = +tile.dataset.count;
        tile.querySelector('.tile-tally').innerHTML = makeTallySVG(count, true);
        tile.querySelector('.tile-num').textContent = count;
        if (count > 0) tile.classList.add('has-count');

        tile.querySelector('.tile-plus').addEventListener('click', e => {
            e.stopPropagation();
            doMark(tile.dataset.vehicleId, tile.dataset.driverId, tile.dataset.materialId);
        });
        tile.querySelector('.tile-minus').addEventListener('click', e => {
            e.stopPropagation();
            doUnmark(tile.dataset.vehicleId, tile.dataset.materialId);
        });
    });

    // ── Badge tổng mobile ──
    updateGlobalBadge();
    document.querySelectorAll('.m-tile').forEach(t => updateVehicleBadge(t.dataset.vehicleId));

    // ── Tiền cước input ──
    document.querySelectorAll('.freight-price-input').forEach(inp => {
        inp.addEventListener('change', function () {
            const vid    = this.dataset.vehicleId;
            const rowIdx = +this.dataset.rowIndex;
            const val    = parseFloat(this.value) || 0;

            if (!vState[vid]) vState[vid] = {};
            vState[vid].freight_price = val;
            syncInputs(vid); // sync desktop ↔ mobile của cùng vehicle

            if (rowIdx === 0) {
                applyAutoFill(0, vid);
            } else {
                this.dataset.autoFilled = '0';
                // sync mobile ↔ desktop của cùng vehicle (không phải row 0)
            }
            scheduleConfigUpdate(vid);
        });
    });

    // ── Route select ──
    document.querySelectorAll('.route-select').forEach(sel => {
        sel.addEventListener('change', function () {
            const vid    = this.dataset.vehicleId;
            const rowIdx = +this.dataset.rowIndex;
            const val    = this.value || null;

            if (!vState[vid]) vState[vid] = {};
            vState[vid].route_id = val;
            syncInputs(vid);

            if (rowIdx === 0) {
                applyAutoFill(0, vid);
            } else {
                this.dataset.autoFilled = '0';
            }
            scheduleConfigUpdate(vid);
        });
    });

    // ── Chi tiết ──
    document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('click', function () {
            showDetails(this.dataset.vehicleId, this.dataset.plate, this.dataset.driver);
        });
    });

    // ── Nhập chuyến ──
    document.getElementById('btnNhapChuyen')?.addEventListener('click', showCommitModal);
    document.getElementById('btnNhapChuyenMobile')?.addEventListener('click', showCommitModal);
    document.getElementById('btnConfirmCommit')?.addEventListener('click', commitTrips);

    // ── Mobile: Popup chọn xe ────────────────────────
    let currentVehicleId = null;

    // Chọn xe cụ thể: hiện đúng 1 card, cập nhật label nút
    function selectVehicle(vehicleId, label) {
        currentVehicleId = vehicleId;
        document.querySelectorAll('.vc-card[data-vehicle-id]').forEach(card => {
            card.classList.toggle('d-none', card.dataset.vehicleId !== String(vehicleId));
        });
        const lbl = document.getElementById('mSelectedLabel');
        if (lbl) lbl.textContent = label;
        // Highlight pill được chọn
        document.querySelectorAll('.picker-vehicle-item').forEach(p => {
            p.classList.toggle('selected', p.dataset.vehicleId === String(vehicleId));
        });
        bootstrap.Modal.getInstance(document.getElementById('vehiclePickerModal'))?.hide();
    }

    // Hiện tất cả xe
    function showAllVehicles() {
        currentVehicleId = null;
        document.querySelectorAll('.vc-card[data-vehicle-id]').forEach(c => c.classList.remove('d-none'));
        const lbl = document.getElementById('mSelectedLabel');
        if (lbl) lbl.textContent = 'Tất cả xe';
        bootstrap.Modal.getInstance(document.getElementById('vehiclePickerModal'))?.hide();
    }

    // Click item trong popup
    document.querySelectorAll('.picker-vehicle-item').forEach(item => {
        item.addEventListener('click', function () {
            selectVehicle(this.dataset.vehicleId, this.dataset.label);
        });
    });

    // Nút "Hiện tất cả" trong popup
    document.getElementById('btnShowAllFromModal')?.addEventListener('click', showAllVehicles);

    // Tìm kiếm trong popup
    function filterPickers(query) {
        const q = query.toLowerCase().trim();
        document.querySelectorAll('.picker-vehicle-item').forEach(item => {
            const hay = (item.getAttribute('data-search') || '').toLowerCase();
            item.classList.toggle('vc-hidden', !!(q && !hay.includes(q)));
        });
    }

    const searchEl = document.getElementById('vehicleSearch');
    if (searchEl) {
        searchEl.addEventListener('input', function () { filterPickers(this.value); });
        searchEl.addEventListener('keyup',  function () { filterPickers(this.value); });
    }

    // Reset ô tìm khi mở lại modal
    document.getElementById('vehiclePickerModal')?.addEventListener('show.bs.modal', function () {
        const s = document.getElementById('vehicleSearch');
        if (s) { s.value = ''; s.focus(); }
        document.querySelectorAll('.picker-vehicle-item').forEach(i => i.classList.remove('vc-hidden'));
    });
});
</script>
@endpush
@endsection
