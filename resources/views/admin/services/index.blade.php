@extends('admin.layout')

@section('pageTitle', 'Manajemen Services')

@section('content')
    <div class="table-card glass-card">
        <div class="table-header">
            <div>
                <p class="eyebrow">Daftar Services</p>
                <h2>Commission services & pricing</h2>
            </div>
            <a href="{{ route('admin.services.create') }}" class="button button-soft">+ Tambah Service</a>
        </div>

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

        @if($services->count() > 0)
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td>
                                    @php
                                        $previewArtwork = $latestArtworks[$service->id] ?? null;
                                    @endphp
                                    @if($previewArtwork && $previewArtwork->image)
                                        <img src="{{ asset('storage/' . $previewArtwork->image) }}" alt="{{ $service->name }}" class="artwork-thumbnail" title="Latest artwork using {{ $service->name }}">
                                    @else
                                        <div class="thumbnail-placeholder">
                                            <span class="material-icons-outlined">image</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="art-title">{{ $service->name }}</td>
                                <td>$ {{ number_format($service->starting_price, 0, '.', ',') }}</td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-warning">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="actions">
                                    <a href="{{ route('admin.services.show', $service) }}" class="button button-outline button-sm">Detail</a>
                                    <a href="{{ route('admin.services.edit', $service) }}" class="button button-soft button-sm">Edit</a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin?');">
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
        @else
            <div class="empty-state">
                <span class="material-icons-outlined">miscellaneous_services</span>
                <h3>Belum ada service</h3>
                <p>Mulai dengan menambahkan service baru</p>
                <a href="{{ route('admin.services.create') }}" class="button button-primary">Tambah Service</a>
            </div>
        @endif
    </div>
@endsection