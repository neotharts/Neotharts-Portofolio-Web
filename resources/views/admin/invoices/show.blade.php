@extends('admin.layout')

@section('pageTitle', 'Detail Invoice - ' . $invoice->invoice_number)

@push('styles')
<style>
    /* Color Variables */
    :root {
        --inv-primary: #ff9543;
        --inv-primary-dark: #e67a30;
        --inv-success: #22c55e;
        --inv-danger: #ef4444;
    }

    .invoice-detail-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f1b18;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h1 .material-icons-outlined {
        color: var(--inv-primary);
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: #fff7ef;
        color: #8c7f74;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
        border: 1px solid rgba(31, 27, 24, 0.08);
    }

    .back-btn:hover {
        background: #f3e6d8;
        color: #1f1b18;
    }

    /* Invoice Card */
    .invoice-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid rgba(31, 27, 24, 0.08);
    }

    .invoice-header {
        background: linear-gradient(135deg, #1f1b18 0%, #3d3832 100%);
        color: #fff;
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .invoice-info h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: var(--inv-primary);
    }

    .invoice-number {
        color: #d4ccc4;
        font-size: 0.9rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.unpaid { background: #ef4444; }
    .status-badge.sketch { background: #eab308; color: #1f1b18; }
    .status-badge.progress { background: #3b82f6; }
    .status-badge.finishing { background: #8b5cf6; }
    .status-badge.done { background: #22c55e; }

    .invoice-body {
        padding: 2rem;
    }

    /* Client Info */
    .client-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(31, 27, 24, 0.08);
    }

    .section-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: #8c7f74;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title .material-icons-outlined {
        font-size: 1rem;
        color: var(--inv-primary);
    }

    .client-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .client-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .client-item label {
        font-size: 0.8rem;
        color: #8c7f74;
    }

    .client-item span {
        font-size: 1rem;
        font-weight: 600;
        color: #1f1b18;
    }

    /* Items Table */
    .items-section {
        margin-bottom: 2rem;
    }

    .items-table-wrapper {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid rgba(31, 27, 24, 0.08);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead {
        background: linear-gradient(135deg, var(--inv-primary) 0%, var(--inv-primary-dark) 100%);
    }

    .items-table thead th {
        padding: 1rem;
        text-align: left;
        font-size: 0.85rem;
        font-weight: 600;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .items-table tbody tr {
        border-bottom: 1px solid #fff7ef;
    }

    .items-table tbody tr:last-child {
        border-bottom: none;
    }

    .items-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .items-table .service-name {
        font-weight: 600;
        color: #1f1b18;
    }

    .items-table .usage-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .usage-badge.personal {
        background: #dbeafe;
        color: #2563eb;
    }

    .usage-badge.commercial {
        background: #fce7f3;
        color: #db2777;
    }

    .items-table .price {
        font-weight: 600;
        color: #1f1b18;
    }

    /* Total Section */
    .total-section {
        display: flex;
        justify-content: flex-end;
        padding: 1.5rem;
        background: #fff7ef;
        border-radius: 12px;
        margin-top: 1rem;
    }

    .total-box {
        text-align: right;
    }

    .total-label {
        font-size: 0.9rem;
        color: #8c7f74;
        margin-bottom: 0.5rem;
    }

    .total-amount {
        font-size: 2rem;
        font-weight: 700;
        color: var(--inv-primary);
    }

    /* Payment Info */
    .payment-section {
        margin-top: 2rem;
        padding: 1.5rem;
        background: #fbf5ed;
        border-radius: 12px;
        border: 1px solid #f3e6d8;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .payment-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .payment-icon {
        width: 48px;
        height: 48px;
        background: var(--inv-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.5rem;
    }

    .payment-label {
        font-size: 0.85rem;
        color: #8c7f74;
    }

    .payment-method {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f1b18;
        text-transform: uppercase;
    }

    /* Footer Actions */
    .invoice-footer {
        padding: 1.5rem 2rem;
        background: #fff7ef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .footer-info {
        font-size: 0.85rem;
        color: #8c7f74;
    }

    .footer-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--inv-primary) 0%, var(--inv-primary-dark) 100%);
        color: #fff;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--inv-primary-dark) 0%, #cc6b2a 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 149, 67, 0.3);
    }

    .btn-download {
        background: linear-gradient(135deg, var(--inv-success) 0%, #16a34a 100%);
        color: #fff;
    }

    .btn-download:hover {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    .btn-danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--inv-danger);
    }

    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .invoice-detail-container {
            padding: 1rem;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .invoice-header {
            flex-direction: column;
        }

        .client-grid {
            grid-template-columns: 1fr;
        }

        .total-section {
            justify-content: center;
            text-align: center;
        }

        .invoice-footer {
            flex-direction: column;
        }

        .footer-actions {
            width: 100%;
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="invoice-detail-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <span class="material-icons-outlined">receipt_long</span>
            Detail Invoice
        </h1>
        <a href="{{ route('admin.invoices.index') }}" class="back-btn">
            <span class="material-icons-outlined">arrow_back</span>
            Kembali
        </a>
    </div>

    <!-- Invoice Card -->
    <div class="invoice-card">
        <div class="invoice-header">
            <div class="invoice-info">
                <h2>Neotharts</h2>
                <p class="invoice-number">{{ $invoice->invoice_number }}</p>
                <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem; flex-wrap: wrap;">
                    <span style="font-size: 0.85rem; color: #94a3b8;">
                        <span class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">mail</span>
                        neothartsofficial@gmail.com
                    </span>
                    <span style="font-size: 0.85rem; color: #94a3b8;">
                        <span class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">camera_alt</span>
                        @neotharts
                    </span>
                </div>
            </div>
            <span class="status-badge {{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
        </div>

        <div class="invoice-body">
            <!-- Client Info -->
            <div class="client-section">
                <h3 class="section-title">
                    <span class="material-icons-outlined">person</span>
                    Informasi Client
                </h3>
                <div class="client-grid">
                    <div class="client-item">
                        <label>Nama Client</label>
                        <span>{{ $invoice->client_name }}</span>
                    </div>
                    <div class="client-item">
                        <label>Email</label>
                        <span>{{ $invoice->client_email ?: '-' }}</span>
                    </div>
                    <div class="client-item">
                        <label>Instagram</label>
                        <span>{{ $invoice->client_instagram ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="items-section">
                <h3 class="section-title">
                    <span class="material-icons-outlined">list_alt</span>
                    Detail Service
                </h3>
                <div class="items-table-wrapper">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Add Character</th>
                                <th>Kegunaan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="service-name">{{ $item->service_name }}</td>
                                    @if($invoice->currency === 'IDR')
                                        <td>Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td class="price">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    @else
                                        <td>${{ number_format($item->unit_price / 16000, 2, '.', ',') }}</td>
                                        <td class="price">${{ number_format($item->subtotal / 16000, 2, '.', ',') }}</td>
                                    @endif
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->additional_characters }}</td>
                                    <td>
                                        <span class="usage-badge {{ $item->usage_type }}">
                                            {{ ucfirst($item->usage_type) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total -->
            <div class="total-section">
                <div class="total-box">
                    <p class="total-label">Total Pembayaran</p>
                    @if($invoice->currency === 'IDR')
                        <p class="total-amount">Rp{{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                    @else
                        <p class="total-amount">${{ number_format($invoice->total_amount / 16000, 2, '.', ',') }}</p>
                    @endif
                </div>
            </div>

            <!-- Payment Method -->
            <div class="payment-section">
                <div class="payment-info">
                    <div class="payment-icon">
                        <span class="material-icons-outlined">
                            {{ $invoice->payment_method === 'qris' ? 'qr_code_2' : 'account_balance' }}
                        </span>
                    </div>
                    <div>
                        <p class="payment-label">Metode Pembayaran</p>
                        <p class="payment-method">{{ $invoice->payment_method === 'qris' ? 'QRIS' : 'PayPal' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p class="footer-info">
                Dibuat: {{ $invoice->created_at->format('d M Y, H:i') }}
            </p>
            <div class="footer-actions">
                <a href="{{ route('admin.invoices.pdf', $invoice) }}" class="btn btn-download" target="_blank">
                    <span class="material-icons-outlined">download</span>
                    Download PDF
                </a>
                <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <span class="material-icons-outlined">delete</span>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection