@extends('admin.layouts.app')

@section('title', 'Kelola Menu & Stok')
@section('page-title', '📖 Kelola Menu & Stok')

@section('styles')
<style>
    .menu-img-placeholder {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        background: linear-gradient(135deg, rgba(6,182,212,0.12), rgba(6,182,212,0.06));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #06b6d4;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stock-badge-ok {
        background: rgba(34,197,94,0.1);
        color: #16a34a;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .stock-badge-low {
        background: rgba(239,68,68,0.1);
        color: #dc2626;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }
    .btn-toggle-on {
        background: rgba(239,68,68,0.08);
        color: #dc2626;
        border: 1px solid rgba(239,68,68,0.2);
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-toggle-on:hover { background: rgba(239,68,68,0.15); }
    .btn-toggle-off {
        background: rgba(34,197,94,0.08);
        color: #16a34a;
        border: 1px solid rgba(34,197,94,0.2);
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-toggle-off:hover { background: rgba(34,197,94,0.15); }
    .cat-badge {
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .modal-content { border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
    .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 24px; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 16px 24px; }
    .form-control, .form-select {
        border-radius: 9px;
        border: 1.5px solid #e2e8f0;
        font-size: 13.5px;
        padding: 9px 14px;
        transition: border-color 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #06b6d4;
        box-shadow: 0 0 0 3px rgba(6,182,212,0.1);
    }
    .form-label { font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 6px; }
</style>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="font-size:20px;color:#0f172a;">Kelola Menu & Stok</h2>
        <p class="mb-0" style="font-size:13px;color:#94a3b8;">Tambah menu baru, atur varian, dan toggle status ketersediaan stok.</p>
    </div>
    <button type="button" class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#addMenuModal">
        <i class="fa-solid fa-plus me-2"></i> Tambah Menu Baru
    </button>
</div>

<div class="card-panel">
    <div class="table-responsive">
        <table class="table table-panel mb-0">
            <thead>
                <tr>
                    <th style="padding-left:24px;">Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th style="text-align:right;padding-right:24px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                <tr>
                    <td style="padding-left:24px;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="menu-img-placeholder">
                                <i class="fa-solid fa-utensils"></i>
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:14px;color:#0f172a;">{{ $menu->name }}</div>
                                <div style="font-size:12px;color:#94a3b8;margin-top:1px;">{{ Str::limit($menu->description, 45) }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="cat-badge">{{ $menu->category->name ?? '-' }}</span></td>
                    <td style="font-weight:700;color:#0f172a;">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                    <td>
                        @if($menu->stock > 10)
                            <span class="stock-badge-ok"><i class="fa-solid fa-circle me-1" style="font-size:7px;"></i>{{ $menu->stock }} pcs</span>
                        @elseif($menu->stock > 0)
                            <span class="stock-badge-low"><i class="fa-solid fa-triangle-exclamation me-1" style="font-size:10px;"></i>{{ $menu->stock }} pcs</span>
                        @else
                            <span class="stock-badge-low"><i class="fa-solid fa-ban me-1" style="font-size:10px;"></i>Habis</span>
                        @endif
                    </td>
                    <td>
                        @if($menu->status && $menu->stock > 0)
                            <span style="background:rgba(34,197,94,0.1);color:#16a34a;font-size:11.5px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                ✓ Tersedia
                            </span>
                        @else
                            <span style="background:rgba(239,68,68,0.1);color:#dc2626;font-size:11.5px;font-weight:700;padding:5px 12px;border-radius:20px;">
                                ✗ Habis
                            </span>
                        @endif
                    </td>
                    <td style="text-align:right;padding-right:24px;">
                        <form action="{{ route('admin.menus.toggle-stock', $menu->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="{{ $menu->status ? 'btn-toggle-on' : 'btn-toggle-off' }}">
                                @if($menu->status)
                                    <i class="fa-solid fa-ban me-1"></i> Set Habis
                                @else
                                    <i class="fa-solid fa-check me-1"></i> Set Tersedia
                                @endif
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:#94a3b8;">
                        <i class="fa-solid fa-utensils fa-2x mb-2 d-block"></i>
                        Belum ada menu. Tambahkan menu pertama Anda!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" style="font-size:16px;">Tambah Menu Baru</h5>
                        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Isi detail menu yang akan ditambahkan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select" required>
                            <option value="" disabled selected>Pilih kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" name="name" class="form-control" required placeholder="misal: Dimsum Ayam Mozzarella">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span style="color:#94a3b8;font-weight:400;">(opsional)</span></label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat menu..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" required placeholder="15000" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Awal</label>
                            <input type="number" name="stock" class="form-control" required value="50" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="gap:8px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:9px;font-weight:600;font-size:13.5px;">Batal</button>
                    <button type="submit" class="btn btn-accent">
                        <i class="fa-solid fa-check me-1"></i> Simpan Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
