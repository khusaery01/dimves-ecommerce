@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', '🏠 Dashboard')

@section('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 22px 24px;
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 16px;
    }
    .stat-card .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 6px;
    }
    .stat-card .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .stat-card .stat-sub {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 4px;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        opacity: 0.06;
    }
    .stat-cyan .stat-icon { background: rgba(6,182,212,0.12); color: #06b6d4; }
    .stat-cyan::after { background: #06b6d4; }
    .stat-orange .stat-icon { background: rgba(249,115,22,0.12); color: #f97316; }
    .stat-orange::after { background: #f97316; }
    .stat-green .stat-icon { background: rgba(34,197,94,0.12); color: #22c55e; }
    .stat-green::after { background: #22c55e; }
    .stat-purple .stat-icon { background: rgba(168,85,247,0.12); color: #a855f7; }
    .stat-purple::after { background: #a855f7; }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0;
    }
    .section-sub {
        font-size: 12.5px;
        color: #94a3b8;
        margin-top: 2px;
    }

    .order-status-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:22px; color:#0f172a;">Selamat Datang di DIMVES 👋</h2>
        <p class="mb-0" style="font-size:13px; color:#94a3b8;">
            {{ now()->translatedFormat('l, d F Y') }} — Berikut ringkasan bisnis hari ini.
        </p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-accent">
        <i class="fa-solid fa-fire-burner me-2"></i> Buka Kitchen Display
    </a>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-cyan">
            <div class="stat-icon"><i class="fa-solid fa-wallet"></i></div>
            <div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
            <div class="stat-label">Omzet Hari Ini</div>
            <div class="stat-sub">Bulan ini: Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon"><i class="fa-solid fa-fire"></i></div>
            <div class="stat-value">{{ $activeOrders }}</div>
            <div class="stat-label">Pesanan Aktif</div>
            <div class="stat-sub">Waiting + Preparing + Ready</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-green">
            <div class="stat-icon"><i class="fa-solid fa-utensils"></i></div>
            <div class="stat-value">{{ $availableMenus }}</div>
            <div class="stat-label">Menu Tersedia</div>
            <div class="stat-sub">Stok > 0 & status aktif</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="fa-solid fa-ticket"></i></div>
            <div class="stat-value">{{ $activeVouchers }}</div>
            <div class="stat-label">Voucher Aktif</div>
            <div class="stat-sub">Belum expired</div>
        </div>
    </div>
</div>

{{-- Chart + Recent Orders --}}
<div class="row g-3">
    {{-- Revenue Chart --}}
    <div class="col-xl-8">
        <div class="card-panel p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <p class="section-title">Grafik Omzet 7 Hari Terakhir</p>
                    <p class="section-sub">Total pendapatan pesanan selesai per hari</p>
                </div>
                <span class="badge" style="background:rgba(6,182,212,0.12);color:#06b6d4;font-size:11px;font-weight:600;padding:5px 12px;border-radius:8px;">
                    <i class="fa-solid fa-chart-line me-1"></i> 7 Hari
                </span>
            </div>
            <canvas id="revenueChart" height="110"></canvas>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="col-xl-4">
        <div class="card-panel p-4 h-100">
            <p class="section-title mb-1">Statistik Bulan Ini</p>
            <p class="section-sub mb-4">{{ now()->translatedFormat('F Y') }}</p>

            <div class="d-flex align-items-center justify-content-between py-3" style="border-bottom:1px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:36px;height:36px;background:rgba(6,182,212,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-receipt" style="color:#06b6d4;font-size:14px;"></i>
                    </div>
                    <span style="font-size:13px;font-weight:500;color:#475569;">Total Pesanan</span>
                </div>
                <span style="font-size:16px;font-weight:700;color:#0f172a;">{{ $totalOrders }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between py-3" style="border-bottom:1px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:36px;height:36px;background:rgba(34,197,94,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-money-bill" style="color:#22c55e;font-size:14px;"></i>
                    </div>
                    <span style="font-size:13px;font-weight:500;color:#475569;">Total Omzet</span>
                </div>
                <span style="font-size:14px;font-weight:700;color:#0f172a;">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:36px;height:36px;background:rgba(249,115,22,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-calculator" style="color:#f97316;font-size:14px;"></i>
                    </div>
                    <span style="font-size:13px;font-weight:500;color:#475569;">Rata-rata / Pesanan</span>
                </div>
                <span style="font-size:14px;font-weight:700;color:#0f172a;">
                    Rp {{ $totalOrders > 0 ? number_format($monthRevenue / $totalOrders, 0, ',', '.') : '0' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="card-panel mt-3">
    <div class="d-flex justify-content-between align-items-center p-4" style="border-bottom:1px solid #f1f5f9;">
        <div>
            <p class="section-title">Pesanan Terbaru</p>
            <p class="section-sub">5 pesanan paling baru</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm" style="background:#f1f5f9;color:#475569;font-size:12px;font-weight:600;border-radius:8px;padding:7px 14px;">
            Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-panel mb-0">
            <thead>
                <tr>
                    <th>Kode Order</th>
                    <th>Customer</th>
                    <th>Tipe</th>
                    <th>Item</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr>
                    <td><strong style="font-size:13px;">#{{ $order->order_code }}</strong></td>
                    <td style="color:#475569;">{{ $order->user->name ?? 'Guest' }}</td>
                    <td>
                        <span class="badge" style="background:#f1f5f9;color:#475569;font-size:11px;font-weight:600;border-radius:6px;padding:4px 9px;">
                            {{ strtoupper($order->order_type ?? 'dine-in') }}
                        </span>
                    </td>
                    <td style="color:#64748b;">{{ $order->items->count() }} item</td>
                    <td><strong style="color:#0f172a;">Rp {{ number_format($order->grand_total > 0 ? $order->grand_total : $order->total_price, 0, ',', '.') }}</strong></td>
                    <td>
                        @php
                            $statusColor = match($order->status) {
                                'Selesai' => ['bg' => 'rgba(34,197,94,0.1)', 'color' => '#16a34a'],
                                'Diproses' => ['bg' => 'rgba(249,115,22,0.1)', 'color' => '#ea580c'],
                                'Dibatalkan' => ['bg' => 'rgba(239,68,68,0.1)', 'color' => '#dc2626'],
                                default => ['bg' => 'rgba(148,163,184,0.15)', 'color' => '#64748b'],
                            };
                        @endphp
                        <span class="order-status-badge"
                            style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};">
                            {{ $order->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:#94a3b8;">
                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                        Belum ada pesanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Omzet (Rp)',
                data: {!! json_encode($chartValues) !!},
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6, 182, 212, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#06b6d4',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    },
                    backgroundColor: '#0f172a',
                    titleFont: { size: 12, family: 'Inter' },
                    bodyFont: { size: 13, family: 'Inter' },
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, family: 'Inter' }, color: '#94a3b8' }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { size: 11, family: 'Inter' },
                        color: '#94a3b8',
                        callback: (val) => 'Rp ' + (val / 1000).toFixed(0) + 'K'
                    }
                }
            }
        }
    });
</script>
@endsection
