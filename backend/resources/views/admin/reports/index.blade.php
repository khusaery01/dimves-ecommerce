@extends('admin.layouts.app')

@section('title', 'Laporan Omzet')
@section('page-title', '📊 Laporan Omzet')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:20px;color:#0f172a;">Laporan Rekap Omzet</h2>
        <p class="mb-0" style="font-size:13px;color:#94a3b8;">Statistik omzet penjualan dan menu terlaris DIMVES.</p>
    </div>
</div>

<!-- Filter Tanggal -->
<div class="card-panel mb-4">
    <div style="padding:20px 24px;">
        <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:14px;"><i class="fa-solid fa-filter me-2" style="color:#06b6d4;"></i>Filter Periode</p>
        <form action="{{ route('admin.reports.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label style="font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;display:block;">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}"
                           style="border-radius:9px;border:1.5px solid #e2e8f0;font-size:13.5px;padding:9px 14px;">
                </div>
                <div class="col-md-4">
                    <label style="font-size:12px;font-weight:600;color:#64748b;margin-bottom:6px;display:block;">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}"
                           style="border-radius:9px;border:1.5px solid #e2e8f0;font-size:13.5px;padding:9px 14px;">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-accent w-100">
                        <i class="fa-solid fa-search me-2"></i> Tampilkan Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:14px;padding:24px;position:relative;overflow:hidden;">
            <div style="position:absolute;right:-20px;top:-20px;width:100px;height:100px;background:rgba(6,182,212,0.15);border-radius:50%;"></div>
            <p style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Total Omzet</p>
            <h2 style="font-size:28px;font-weight:800;color:#f1f5f9;margin-bottom:4px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            <p style="font-size:12px;color:#475569;margin:0;">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            <i class="fa-solid fa-wallet" style="position:absolute;right:24px;bottom:20px;font-size:32px;color:rgba(6,182,212,0.25);"></i>
        </div>
    </div>
    <div class="col-md-6">
        <div style="background:linear-gradient(135deg,#0f172a,#1e293b);border-radius:14px;padding:24px;position:relative;overflow:hidden;">
            <div style="position:absolute;right:-20px;top:-20px;width:100px;height:100px;background:rgba(34,197,94,0.12);border-radius:50%;"></div>
            <p style="font-size:11px;font-weight:700;color:#94a3b8;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Total Pesanan</p>
            <h2 style="font-size:28px;font-weight:800;color:#f1f5f9;margin-bottom:4px;">{{ $totalOrders }} Pesanan</h2>
            <p style="font-size:12px;color:#475569;margin:0;">
                Rata-rata: Rp {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 0, ',', '.') : '0' }} / pesanan
            </p>
            <i class="fa-solid fa-receipt" style="position:absolute;right:24px;bottom:20px;font-size:32px;color:rgba(34,197,94,0.2);"></i>
        </div>
    </div>
</div>

<!-- Top Menu Table -->
<div class="card-panel">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
        <div style="width:36px;height:36px;background:rgba(251,191,36,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;">
            <i class="fa-solid fa-crown" style="color:#f59e0b;font-size:15px;"></i>
        </div>
        <div>
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Top 5 Menu Terlaris</p>
            <p style="font-size:12px;color:#94a3b8;margin:0;">Berdasarkan periode yang dipilih</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-panel mb-0">
            <thead>
                <tr>
                    <th style="padding-left:24px;">#</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Terjual</th>
                    <th style="text-align:right;padding-right:24px;">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topMenus as $index => $item)
                <tr>
                    <td style="padding-left:24px;">
                        @if($index == 0)
                            <span style="width:28px;height:28px;background:rgba(251,191,36,0.15);color:#d97706;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                                <i class="fa-solid fa-crown"></i>
                            </span>
                        @else
                            <span style="width:28px;height:28px;background:#f1f5f9;color:#64748b;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">
                                {{ $index + 1 }}
                            </span>
                        @endif
                    </td>
                    <td style="font-weight:700;color:#0f172a;">{{ $item->menu->name ?? 'Menu' }}</td>
                    <td><span style="background:#f1f5f9;color:#475569;font-size:11px;font-weight:600;padding:4px 10px;border-radius:6px;">{{ $item->menu->category->name ?? '-' }}</span></td>
                    <td>
                        <span style="background:rgba(6,182,212,0.1);color:#0891b2;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;">
                            {{ $item->total_sold }} porsi
                        </span>
                    </td>
                    <td style="text-align:right;padding-right:24px;font-weight:700;color:#16a34a;">
                        Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5" style="color:#94a3b8;">
                        <i class="fa-solid fa-chart-bar fa-2x mb-2 d-block"></i>
                        Belum ada data penjualan pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
