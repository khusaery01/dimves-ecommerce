@extends('admin.layouts.app')

@section('title', 'Voucher & Promo')
@section('page-title', '🎟️ Voucher & Promo')

@section('styles')
<style>
    .voucher-card {
        background: white;
        border-radius: 14px;
        padding: 20px 22px;
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .voucher-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .voucher-card.inactive { opacity: 0.6; }
    .voucher-code {
        font-family: monospace;
        font-size: 16px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #06b6d4;
        background: rgba(6,182,212,0.08);
        padding: 4px 12px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 8px;
    }
    .voucher-code.inactive-code { color: #94a3b8; background: #f1f5f9; }
    .voucher-name { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .voucher-desc { font-size: 12px; color: #94a3b8; margin-bottom: 12px; }
    .voucher-meta { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
    .voucher-tag {
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 6px;
    }
    .tag-discount { background: rgba(168,85,247,0.1); color: #9333ea; }
    .tag-quota { background: rgba(249,115,22,0.1); color: #ea580c; }
    .tag-date { background: rgba(100,116,139,0.1); color: #475569; }
    .tag-min { background: rgba(6,182,212,0.1); color: #0891b2; }
    .voucher-actions { display: flex; gap: 8px; }
    .btn-deactivate {
        font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 8px;
        background: rgba(239,68,68,0.08); color: #dc2626;
        border: 1px solid rgba(239,68,68,0.2); transition: all 0.2s;
    }
    .btn-deactivate:hover { background: rgba(239,68,68,0.15); }
    .btn-activate {
        font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 8px;
        background: rgba(34,197,94,0.08); color: #16a34a;
        border: 1px solid rgba(34,197,94,0.2); transition: all 0.2s;
    }
    .btn-activate:hover { background: rgba(34,197,94,0.15); }
    .btn-delete {
        font-size: 12px; font-weight: 600; padding: 6px 12px; border-radius: 8px;
        background: rgba(239,68,68,0.05); color: #ef4444;
        border: 1px solid rgba(239,68,68,0.15); transition: all 0.2s;
    }
    .active-dot {
        width: 8px; height: 8px; border-radius: 50%; background: #22c55e;
        display: inline-block; margin-right: 6px;
        animation: pulse-dot 2s infinite;
    }
    .inactive-dot {
        width: 8px; height: 8px; border-radius: 50%; background: #94a3b8;
        display: inline-block; margin-right: 6px;
    }
    .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 24px; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 16px 24px; }
    .form-control, .form-select {
        border-radius: 9px; border: 1.5px solid #e2e8f0;
        font-size: 13.5px; padding: 9px 14px; transition: border-color 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6,182,212,0.1);
    }
    .form-label { font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .empty-state { text-align: center; padding: 80px 20px; }
    .empty-icon {
        width: 80px; height: 80px; background: rgba(6,182,212,0.08); border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px; font-size: 30px; color: #06b6d4;
    }
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:20px;color:#0f172a;">Voucher & Promo</h2>
        <p class="mb-0" style="font-size:13px;color:#94a3b8;">Kelola kode voucher diskon dan promo untuk pelanggan.</p>
    </div>
    <button type="button" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
        <i class="fa-solid fa-plus me-2"></i> Tambah Voucher
    </button>
</div>

@if($vouchers->isEmpty())
<div class="card-panel">
    <div class="empty-state">
        <div class="empty-icon"><i class="fa-solid fa-ticket"></i></div>
        <h5 style="font-weight:700;color:#0f172a;margin-bottom:6px;">Belum Ada Voucher</h5>
        <p style="color:#94a3b8;font-size:13.5px;">Tambahkan voucher pertama untuk menarik lebih banyak pelanggan.</p>
        <button type="button" class="btn btn-accent mt-2" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
            <i class="fa-solid fa-plus me-2"></i> Tambah Voucher Pertama
        </button>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($vouchers as $v)
    <div class="col-md-6 col-lg-4">
        <div class="voucher-card {{ !$v->is_active ? 'inactive' : '' }}">
            <!-- Active indicator -->
            <div style="position:absolute;top:16px;right:18px;font-size:11.5px;font-weight:600;color:{{ $v->is_active ? '#16a34a' : '#94a3b8' }}">
                @if($v->is_active)
                    <span class="active-dot"></span> Aktif
                @else
                    <span class="inactive-dot"></span> Nonaktif
                @endif
            </div>

            <div class="voucher-code {{ !$v->is_active ? 'inactive-code' : '' }}">{{ $v->voucher_code }}</div>
            <div class="voucher-name">{{ $v->name }}</div>
            <div class="voucher-desc">{{ $v->description ?: 'Tidak ada deskripsi' }}</div>

            <div class="voucher-meta">
                <span class="voucher-tag tag-discount">
                    @if($v->discount_type == 'percentage')
                        <i class="fa-solid fa-percent me-1"></i>{{ $v->discount_value }}% OFF
                    @else
                        <i class="fa-solid fa-tag me-1"></i>Rp {{ number_format($v->discount_value, 0, ',', '.') }} OFF
                    @endif
                </span>
                @if($v->min_order > 0)
                    <span class="voucher-tag tag-min">
                        <i class="fa-solid fa-cart-shopping me-1"></i>Min Rp {{ number_format($v->min_order, 0, ',', '.') }}
                    </span>
                @endif
                @if($v->quota)
                    <span class="voucher-tag tag-quota">
                        <i class="fa-solid fa-users me-1"></i>{{ $v->used_count }}/{{ $v->quota }} dipakai
                    </span>
                @endif
                <span class="voucher-tag tag-date">
                    <i class="fa-regular fa-calendar me-1"></i>
                    {{ \Carbon\Carbon::parse($v->end_date)->format('d M Y') }}
                </span>
            </div>

            <div class="voucher-actions">
                <form action="{{ route('admin.vouchers.toggle', $v->id) }}" method="POST" class="d-inline">
                    @csrf
                    @if($v->is_active)
                        <button type="submit" class="btn-deactivate">
                            <i class="fa-solid fa-pause me-1"></i> Nonaktifkan
                        </button>
                    @else
                        <button type="submit" class="btn-activate">
                            <i class="fa-solid fa-play me-1"></i> Aktifkan
                        </button>
                    @endif
                </form>
                <form action="{{ route('admin.vouchers.destroy', $v->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin hapus voucher {{ $v->voucher_code }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- Modal Tambah Voucher -->
<div class="modal fade" id="addVoucherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size:16px;">Tambah Voucher Baru</h5>
                        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Isi detail voucher/promo diskon</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <div class="mb-3">
                        <label class="form-label">Nama Promo</label>
                        <input type="text" name="name" class="form-control" required placeholder="misal: Promo Hari Jadi DIMVES">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Voucher</label>
                        <input type="text" name="voucher_code" class="form-control" required placeholder="misal: DIMVES50"
                               style="font-family:monospace;letter-spacing:1px;text-transform:uppercase;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe Diskon</label>
                            <select name="discount_type" class="form-select" required id="discountType">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Diskon</label>
                            <input type="number" name="discount_value" class="form-control" required placeholder="50" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Order (Rp)</label>
                            <input type="number" name="min_order" class="form-control" placeholder="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota <span style="color:#94a3b8;font-weight:400;">(kosongkan = unlimited)</span></label>
                            <input type="number" name="quota" class="form-control" placeholder="unlimited" min="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Berakhir</label>
                            <input type="date" name="end_date" class="form-control" required value="{{ now()->addMonth()->toDateString() }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="gap:8px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:9px;font-weight:600;font-size:13.5px;">Batal</button>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-ticket me-1"></i> Simpan Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
