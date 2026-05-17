@extends('admin.layout')

@section('pageTitle', 'Detail Service')

@section('content')
    <div class="detail-card glass-card">
        <div class="detail-header">
            <div>
                <p class="eyebrow">Detail Service</p>
                <h2>{{ $service->name }}</h2>
            </div>
            <div class="detail-actions">
                <a href="{{ route('admin.services.edit', $service) }}" class="button button-soft">Edit</a>
                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-danger">Hapus</button>
                </form>
                <a href="{{ route('admin.services.index') }}" class="button button-outline">Kembali</a>
            </div>
        </div>

        <div class="detail-content">
            <div class="detail-image-section">
                @if($latestArtwork && $latestArtwork->image)
                    <img src="{{ asset('storage/' . $latestArtwork->image) }}" alt="{{ $service->name }}" class="detail-image">
                    <p class="muted-text image-caption">Latest artwork using {{ $service->name }}</p>
                @else
                    <div class="no-image-placeholder">
                        <span class="material-icons-outlined">image</span>
                        <p>Belum ada artwork yang menggunakan service ini</p>
                    </div>
                @endif
            </div>

            <div class="detail-info-section">
                @if($service->description)
                <div class="info-group">
                    <h3>Deskripsi</h3>
                    <p>{{ $service->description }}</p>
                </div>
                @endif

                <div class="info-group">
                    <h3>Harga</h3>
                    <p class="price-display">Rp {{ number_format($service->starting_price, 0, ',', '.') }}</p>
                </div>

                @if(count($service->features_array) > 0)
                <div class="info-group">
                    <h3>Fitur</h3>
                    <ul class="features-list">
                        @foreach($service->features_array as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="info-group">
                    <h3>Status</h3>
                    @if($service->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-warning">Nonaktif</span>
                    @endif
                </div>

                <div class="info-group">
                    <h3>Informasi Tambahan</h3>
                    <p>Urutan: {{ $service->sort_order }}</p>
                    <p>Dibuat: {{ $service->created_at->format('d F Y H:i') }}</p>
                    @if($service->updated_at !== $service->created_at)
                        <p>Diperbarui: {{ $service->updated_at->format('d F Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .image-caption {
        text-align: center;
        font-size: 12px;
        margin-top: 8px;
    }
</style>