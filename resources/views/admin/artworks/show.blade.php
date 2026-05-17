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
                    </div>
                </div>

                @if($artwork->list_service && count($artwork->list_service) > 0)
                <div class="info-group">
                    <h3>List Service</h3>
                    <div class="service-badges">
                        @foreach($artwork->list_service as $service)
                            <span class="service-badge tag-{{ $service }}">{{ ucfirst(str_replace('-', ' ', $service)) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

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
                    <h3>Art For</h3>
                    <p>{{ $artwork->art_for ?? 'myself' }}</p>
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

    <style>
        .service-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .service-badge {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 500;
        }

        .tag-headshot { background: #e8a87c; color: white; }
        .tag-halfbody { background: #c38c9c; color: white; }
        .tag-fullbody { background: #85c1ae; color: white; }
        .tag-chibi { background: #9bc1e8; color: white; }
    </style>
@endsection