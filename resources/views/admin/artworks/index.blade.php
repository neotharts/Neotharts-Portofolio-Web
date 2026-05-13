@extends('admin.layout')

@section('pageTitle', 'Manajemen Artwork')

@section('content')
    <div class="table-card glass-card">
        <div class="table-header">
            <div>
                <p class="eyebrow">Daftar Artwork</p>
                <h2>Portfolio koleksi artwork</h2>
            </div>
            <a href="{{ route('admin.artworks.create') }}" class="button button-soft">+ Tambah Artwork</a>
        </div>

        <!-- Filter & Search -->
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.artworks.index') }}" class="filter-form">
                <input type="text" name="search" placeholder="Cari artwork..." value="{{ request('search') }}" class="filter-input">
                
                <select name="type" class="filter-select">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>

                <select name="form" class="filter-select">
                    <option value="">Semua Form</option>
                    @foreach($forms as $form)
                        <option value="{{ $form }}" {{ request('form') === $form ? 'selected' : '' }}>
                            {{ ucfirst($form) }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="filter-select">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>

                <button type="submit" class="button button-primary">Filter</button>
                @if(request()->anyFilled(['search', 'type', 'form', 'status']))
                    <a href="{{ route('admin.artworks.index') }}" class="button button-outline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Alert Messages -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-error">
                {{ $message }}
            </div>
        @endif

        <!-- Table -->
        @if($artworks->count() > 0)
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Form</th>
                            <th>Status</th>
                            <th>Upload</th>
                            <th>Art For</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($artworks as $artwork)
                            <tr>
                                <td>
                                    @if($artwork->image)
                                        <img src="{{ asset('storage/' . $artwork->image) }}" alt="{{ $artwork->title }}" class="artwork-thumbnail">
                                    @else
                                        <img src="https://via.placeholder.com/80x60/E8E8E8/999999?text=No+Image" alt="No Image" class="artwork-thumbnail">
                                    @endif
                                </td>
                                <td class="art-title">{{ $artwork->title }}</td>
                                <td>
                                    <span class="badge tag-{{ $artwork->type }}">{{ ucfirst($artwork->type) }}</span>
                                </td>
                                <td>
                                    <span class="badge tag-{{ $artwork->form }}">{{ ucfirst($artwork->form) }}</span>
                                </td>
                                <td>
                                    @if($artwork->is_published)
                                        <span class="badge badge-success">✓ Publik</span>
                                    @else
                                        <span class="badge badge-warning">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $artwork->created_at->format('d M Y') }}</td>
                                <td>{{ $artwork->art_for ?? 'myself' }}</td>
                                <td class="actions">
                                    <a href="{{ route('admin.artworks.show', $artwork) }}" class="button button-outline button-sm">Detail</a>
                                    <a href="{{ route('admin.artworks.edit', $artwork) }}" class="button button-soft button-sm">Edit</a>
                                    <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-danger button-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $artworks->links('vendor.pagination.default') }}
            </div>
        @else
            <div class="empty-state">
                <span class="material-icons-outlined">image_not_supported</span>
                <h3>Belum ada artwork</h3>
                <p>Mulai dengan menambahkan artwork baru ke dalam portfolio Anda</p>
                <a href="{{ route('admin.artworks.create') }}" class="button button-primary">Tambah Artwork</a>
            </div>
        @endif
    </div>
@endsection
