@extends('admin.layout')

@section('pageTitle', 'Selamat Datang Admin')

@section('content')
    <div class="dashboard-grid">
        <!-- Hero + Stats Row -->
        <div class="dashboard-top-section">
            <div class="hero-card glass-card">
                <div>
                    <p class="eyebrow">Selamat Datang Admin</p>
                    <h2>Kelola karya terbaik dengan cepat dan nyaman</h2>
                    <p class="muted-text">Pantau pengunjung, kelola artwork, dan temukan insight baru untuk portofolio digitalmu.</p>
                </div>
                <div class="hero-icon">
                    <span class="material-icons-outlined">bolt</span>
                </div>
            </div>

            <div class="stats-row-stacked">
                <div class="stat-card glass-card">
                    <div class="stat-icon orange">
                        <span class="material-icons-outlined">people</span>
                    </div>
                    <div>
                        <p class="stat-label">Total Pengunjung</p>
                        <h3>{{ number_format($totalVisitors) }}</h3>
                        <small class="muted-text">{{ $todayVisitors }} hari ini</small>
                    </div>
                </div>
                <div class="stat-card glass-card">
                    <div class="stat-icon cream">
                        <span class="material-icons-outlined">palette</span>
                    </div>
                    <div>
                        <p class="stat-label">Total Artwork</p>
                        <h3>{{ $totalArtworks }}</h3>
                        <small class="muted-text">{{ $totalPublished }} dipublikasikan</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="visitor-card glass-card full-width">
            <div class="card-header">
                <div>
                    <p class="eyebrow">Statistik Pengunjung</p>
                    <h3>Tren 7 hari terakhir</h3>
                </div>
                <span class="badge">{{ $todayVisitors }} pengunjung hari ini</span>
            </div>

            <div class="chart-panel">
                <div class="chart-axis">
                    <span>0</span>
                    <span>{{ ceil(max($chartData->toArray() ?: [0]) / 2000) * 2000 }}</span>
                </div>
                <div class="chart-lines">
                    @if($chartData->count() > 0)
                        @php
                            $maxValue = max($chartData->toArray());
                        @endphp
                        @foreach($chartData as $data)
                            <div class="chart-line" style="height: {{ ($data * 100) / $maxValue }}%;"></div>
                        @endforeach
                    @else
                        <p class="muted-text" style="text-align: center; padding: 2rem;">Belum ada data pengunjung</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Artwork Terbaru -->
        <div class="recent-artworks glass-card full-width">
            <div class="card-header">
                <div>
                    <p class="eyebrow">Artwork Terbaru</p>
                    <h3>Karya yang baru diupload</h3>
                </div>
                <a href="{{ route('admin.artworks.index') }}" class="link">Lihat semua</a>
            </div>

            @if($recentArtworks->count() > 0)
                <div class="artworks-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Services</th>
                                <th>Status</th>
                                <th>Tanggal Upload</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentArtworks as $artwork)
                                <tr>
                                    <td>{{ $artwork->title }}</td>
                                    <td><span class="badge-type">{{ ucfirst($artwork->type) }}</span></td>
                                    <td>
                                        @if($artwork->list_service && count($artwork->list_service) > 0)
                                            <div class="service-badges-mini">
                                                @foreach($artwork->list_service as $service)
                                                    <span class="service-badge-mini service-{{ $service }}">{{ ucfirst(str_replace('-', ' ', $service)) }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="muted-text">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($artwork->is_published)
                                            <span class="badge badge-success">Dipublikasikan</span>
                                        @else
                                            <span class="badge badge-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ $artwork->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.artworks.show', $artwork) }}" class="btn-sm">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="muted-text" style="padding: 2rem; text-align: center;">Belum ada artwork</p>
            @endif
        </div>
    </div>
@endsection