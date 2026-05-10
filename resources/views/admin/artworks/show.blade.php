@extends('admin.layout')

@section('pageTitle', 'Detail Artwork')

@section('content')
    <div class="detail-card glass-card">
        <div class="detail-header">
            <div>
                <p class="eyebrow">Detail Artwork</p>
                <h2>{{ $artwork->title }}</h2>
            </div>
            <div class="detail-actions">
                <a href="{{ route('admin.artworks.edit', $artwork) }}" class="button button-soft">Edit</a>
                <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-danger">Hapus</button>
                </form>
                <a href="{{ route('admin.artworks.index') }}" class="button button-outline">Kembali</a>
            </div>
        </div>

        <div class="detail-content">
            <div class="detail-image-section">
                @if($artwork->image)
                    <img src="{{ asset('storage/' . $artwork->image) }}" alt="{{ $artwork->title }}" class="detail-image">
                @else
                    <img src="https://via.placeholder.com/500x400/E8E8E8/999999?text=No+Image" alt="No Image" class="detail-image">
                @endif
            </div>

            <div class="detail-info-section">
                <div class="info-group">
                    <h3>Deskripsi</h3>
                    <p>{{ $artwork->description }}</p>
                </div>

                <div class="info-group">
                    <h3>Kategori</h3>
                    <div class="info-badges">
                        <span class="badge tag-{{ $artwork->type }}">Tipe: {{ ucfirst($artwork->type) }}</span>
                        <span class="badge tag-{{ $artwork->form }}">Form: {{ ucfirst($artwork->form) }}</span>
                    </div>
                </div>

                <div class="info-group">
                    <h3>Status</h3>
                    @if($artwork->is_published)
                        <span class="badge badge-success">✓ Dipublikasikan</span>
                        @if($artwork->published_at)
                            <p class="muted-text">Tanggal publikasi: {{ $artwork->published_at->format('d F Y H:i') }}</p>
                        @endif
                    @else
                        <span class="badge badge-warning">Draft</span>
                    @endif
                </div>

                <div class="info-group">
                    <h3>Artis</h3>
                    <p>{{ $artwork->user->name ?? 'Unknown' }}</p>
                </div>

                <div class="info-group">
                    <h3>Informasi Upload</h3>
                    <p>Dibuat: {{ $artwork->created_at->format('d F Y H:i') }}</p>
                    @if($artwork->created_at !== $artwork->updated_at)
                        <p>Diperbarui: {{ $artwork->updated_at->format('d F Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
