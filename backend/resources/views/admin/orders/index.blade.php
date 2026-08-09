@extends('admin.layouts.app')

@section('title', 'Kitchen Display')
@section('page-title', '🔥 Kitchen Display')

@section('styles')
<style>
    .kds-card {
        background: white; border-radius: 14px; border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04);
        overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; height: 100%;
    }
    .kds-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .kds-card-header { padding: 14px 18px 12px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; }
    .kds-order-code { font-size: 16px; font-weight: 800; color: #0f172a; }
    .kds-status-bar { height: 4px; width: 100%; }
    .status-waiting .kds-status-bar { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .status-preparing .kds-status-bar { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .status-ready .kds-status-bar { background: linear-gradient(90deg, #22c55e, #4ade80); }
    .status-served .kds-status-bar { background: #e2e8f0; }
    .status-pill { font-size: 10.5px; font-weight: 700; padding: 4px 10px; border-radius: 20px; letter-spacing: 0.5px; text-transform: uppercase; }
    .pill-waiting  { background: rgba(245,158,11,0.12); color: #d97706; }
    .pill-preparing { background: rgba(59,130,246,0.12); color: #2563eb; }
    .pill-ready    { background: rgba(34,197,94,0.12); color: #16a34a; }
    .pill-served   { background: rgba(148,163,184,0.12); color: #64748b; }
    @keyframes pulse-waiting {
        0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.3); }
        50% { box-shadow: 0 0 0 6px rgba(245,158,11,0); }
    }
    .status-waiting { animation: pulse-waiting 2.5s infinite; }
    .kds-body { padding: 14px 18px; }
    .kds-customer { font-size: 12px; color: #64748b; margin-bottom: 10px; }
    .kds-items { list-style: none; padding: 0; margin: 0 0 10px; }
    .kds-items li { padding: 9px 0; border-bottom: 1px solid #f8fafc; font-size: 13px; }
    .kds-items li:last-child { border-bottom: none; }
    .item-name { font-weight: 700; color: #0f172a; }
    .item-variant { font-size: 11.5px; color: #f97316; margin-top: 2px; }
    .item-note { font-size: 11.5px; color: #94a3b8; font-style: italic; margin-top: 2px; }
    .kds-footer { padding: 12px 18px 16px; background: #f8fafc; border-top: 1px solid #f1f5f9; }
    .btn-kds { font-size: 12.5px; font-weight: 700; padding: 9px 14px; border-radius: 9px; border: none; transition: all 0.2s; flex: 1; cursor: pointer; }
    .btn-kds:hover { opacity: 0.85; transform: translateY(-1px); }
    .btn-start    { background: #3b82f6; color: white; }
    .btn-ready    { background: #f59e0b; color: white; }
    .btn-done     { background: #22c55e; color: white; }
    .btn-served   { background: #e2e8f0; color: #94a3b8; cursor: default; }
    .btn-cancel   { background: rgba(239,68,68,0.08); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); flex: none; width: 38px; padding: 0; display:flex; align-items:center; justify-content:center; }
    .info-row { display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; color: #64748b; margin-bottom: 6px; }
    .info-row strong { color: #0f172a; font-size: 16px; font-weight: 800; }
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-icon { width: 80px; height: 80px; background: rgba(6,182,212,0.08); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 30px; color: #06b6d4; }
    .voucher-badge { display: inline-flex; align-items: center; gap: 5px; background: rgba(34,197,94,0.10); border: 1px solid rgba(34,197,94,0.25); color: #15803d; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; letter-spacing: 0.3px; }
    .voucher-row { background: rgba(34,197,94,0.05); border: 1px solid rgba(34,197,94,0.15); border-radius: 8px; padding: 8px 12px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:20px;color:#0f172a;">Layar Pesanan Dapur</h2>
        <p class="mb-0" style="font-size:13px;color:#94a3b8;">
            <i class="fa-solid fa-rotate fa-spin me-1" style="color:#06b6d4;"></i>
            Auto-refresh setiap 10 detik
            &nbsp;·&nbsp;
            <span style="color:#f59e0b;font-weight:600;">{{ $orders->where('kitchen_status','waiting')->count() }} waiting</span>
            &nbsp;·&nbsp;
            <span style="color:#3b82f6;font-weight:600;">{{ $orders->where('kitchen_status','preparing')->count() }} preparing</span>
            &nbsp;·&nbsp;
            <span style="color:#22c55e;font-weight:600;">{{ $orders->where('kitchen_status','ready')->count() }} ready</span>
        </p>
    </div>
    <div class="d-flex gap-2">
        <span class="status-pill pill-waiting"><i class="fa-solid fa-clock me-1"></i> Waiting</span>
        <span class="status-pill pill-preparing"><i class="fa-solid fa-fire me-1"></i> Preparing</span>
        <span class="status-pill pill-ready"><i class="fa-solid fa-bell me-1"></i> Ready</span>
    </div>
</div>

<div class="row g-3">
    @forelse($orders as $order)
    @php
        $ks = $order->kitchen_status;
        $statusClass = match($ks) { 'waiting' => 'status-waiting', 'preparing' => 'status-preparing', 'ready' => 'status-ready', default => 'status-served' };
        $pillClass   = match($ks) { 'waiting' => 'pill-waiting', 'preparing' => 'pill-preparing', 'ready' => 'pill-ready', default => 'pill-served' };
        $pillLabel   = match($ks) { 'waiting' => '⏳ Waiting', 'preparing' => '🔥 Preparing', 'ready' => '✅ Ready', default => '✓ Served' };
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="kds-card {{ $statusClass }}">
            <div class="kds-status-bar"></div>
            <div class="kds-card-header">
                <div>
                    <span class="kds-order-code">#{{ $order->order_code }}</span>
                    @if($order->table_number)
                        <span class="badge ms-2" style="background:rgba(239,68,68,0.1);color:#dc2626;font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;">Meja {{ $order->table_number }}</span>
                    @endif
                </div>
                <span class="status-pill {{ $pillClass }}">{{ $pillLabel }}</span>
            </div>
            <div class="kds-body">
                <div class="kds-customer">
                    <i class="fa-solid fa-user me-1"></i> {{ $order->user->name ?? 'Guest' }}
                    &nbsp;·&nbsp; <span style="font-weight:600;text-transform:uppercase;font-size:11px;">{{ $order->order_type ?? 'dine-in' }}</span>
                    &nbsp;·&nbsp; <span>{{ $order->created_at->diffForHumans() }}</span>
                </div>
                <ul class="kds-items">
                    @foreach($order->items as $item)
                    <li>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="item-name">{{ $item->quantity }}× {{ $item->menu->name ?? 'Menu' }}</div>
                                @if($item->variants && count($item->variants) > 0)
                                    <div class="item-variant">
                                        @foreach($item->variants as $var){{ $var->variant_name }}: <strong>{{ $var->option_name }}</strong>{{ !$loop->last ? ' · ' : '' }}@endforeach
                                    </div>
                                @endif
                                @if($item->note)
                                    <div class="item-note"><i class="fa-solid fa-note-sticky me-1"></i>"{{ $item->note }}"</div>
                                @endif
                            </div>
                            <span style="font-size:12.5px;font-weight:700;color:#0f172a;white-space:nowrap;margin-left:8px;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @if($order->notes)
                    <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;padding:8px 12px;font-size:12px;color:#92400e;margin-bottom:10px;">
                        <i class="fa-solid fa-comment-dots me-1"></i> <strong>Catatan:</strong> {{ $order->notes }}
                    </div>
                @endif
                @if($order->voucher_code)
                <div class="voucher-row">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="voucher-badge"><i class="fa-solid fa-ticket"></i> {{ $order->voucher_code }}</span>
                        <span style="font-size:11.5px;color:#64748b;">Voucher dipakai</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <span style="font-size:12px;font-weight:700;color:#16a34a;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    @endif
                </div>
                @endif
                <div class="info-row"><span>Subtotal</span><span style="font-size:12px;color:#475569;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
                @if($order->discount_amount > 0)
                <div class="info-row"><span style="color:#16a34a;">Diskon Voucher</span><span style="font-size:12px;font-weight:600;color:#16a34a;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span></div>
                @endif
                <div class="info-row"><span>Pembayaran</span><span style="font-size:12px;font-weight:600;color:#475569;">{{ $order->payment_method }}</span></div>
                <div class="info-row"><span>Total</span><strong>Rp {{ number_format($order->grand_total > 0 ? $order->grand_total : $order->total_price, 0, ',', '.') }}</strong></div>
            </div>
            <div class="kds-footer">
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                    @csrf
                    @if($ks == 'waiting')
                        <button type="submit" name="kitchen_status" value="preparing" class="btn-kds btn-start"><i class="fa-solid fa-fire me-1"></i> Mulai Dimasak</button>
                    @elseif($ks == 'preparing')
                        <button type="submit" name="kitchen_status" value="ready" class="btn-kds btn-ready"><i class="fa-solid fa-bell me-1"></i> Siap Disajikan</button>
                    @elseif($ks == 'ready')
                        <button type="submit" name="kitchen_status" value="served" class="btn-kds btn-done"><i class="fa-solid fa-circle-check me-1"></i> Selesai Disajikan</button>
                    @else
                        <span class="btn-kds btn-served"><i class="fa-solid fa-check-double me-1"></i> Sudah Selesai</span>
                    @endif
                    @if($ks != 'served')
                    <button type="submit" name="status" value="Dibatalkan" class="btn-kds btn-cancel"
                            onclick="return confirm('Yakin batalkan pesanan ini?')" title="Batalkan">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card-panel empty-state">
            <div class="empty-icon"><i class="fa-solid fa-bowl-food"></i></div>
            <h5 style="font-weight:700;color:#0f172a;margin-bottom:6px;">Dapur Tenang 🎉</h5>
            <p style="color:#94a3b8;font-size:13.5px;">Belum ada pesanan masuk saat ini.</p>
        </div>
    </div>
    @endforelse
</div>

@endsection

@section('scripts')
<script>setTimeout(() => window.location.reload(), 10000);</script>
@endsection
