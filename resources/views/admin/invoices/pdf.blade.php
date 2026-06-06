<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }} | Neotharts</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.45;
            color: #1f1b18;
            background: #f8f3ec;
            padding: 0;
        }

        .invoice-page {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 24px;
        }

        .invoice-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid rgba(31, 27, 24, 0.08);
        }

        .invoice-header-card {
            background: linear-gradient(135deg, #1f1b18 0%, #3d3832 100%);
            border-radius: 16px;
            padding: 24px;
            color: #fff;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 18px;
        }

        .neothart-brand {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .neothart-brand h2 {
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            color: #ff9543;
        }

        .neothart-info {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            color: #d4d0ce;
            font-size: 11px;
        }

        .invoice-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            text-align: right;
            min-width: 220px;
        }

        .invoice-meta .invoice-label {
            font-size: 11px;
            font-weight: 700;
            color: #d4ccc4;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .invoice-number {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
        }

        .invoice-date {
            font-size: 11px;
            color: #d4d0ce;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-badge.unpaid { background: #ef4444; color: #fff; }
        .status-badge.sketch { background: #eab308; color: #1f1b18; }
        .status-badge.progress { background: #3b82f6; color: #fff; }
        .status-badge.finishing { background: #8b5cf6; color: #fff; }
        .status-badge.done { background: #22c55e; color: #fff; }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f1b18;
            margin: 24px 0 16px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            background: #fbf5ed;
            border: 1px solid rgba(31, 27, 24, 0.08);
            border-radius: 12px;
            padding: 16px;
        }

        .info-group label {
            font-size: 10px;
            font-weight: 700;
            color: #8c7f74;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .info-group span {
            font-size: 13px;
            font-weight: 600;
            color: #1f1b18;
        }

        .services-table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
            border: 1px solid rgba(31, 27, 24, 0.08);
            margin-bottom: 20px;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 780px;
            background: #fff;
        }

        .services-table thead {
            background: linear-gradient(135deg, #ff9543 0%, #e67a30 100%);
        }

        .services-table thead th {
            padding: 14px 16px;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .services-table tbody tr {
            border-bottom: 1px solid rgba(31, 27, 24, 0.08);
        }

        .services-table tbody td {
            padding: 14px 16px;
            font-size: 12px;
            vertical-align: middle;
            color: #1f1b18;
        }

        .services-table tbody td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .services-table .service-name {
            font-weight: 700;
            color: #1f1b18;
        }

        .usage-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 700;
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

        .total-summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 24px;
        }

        .total-box {
            background: linear-gradient(135deg, #1f1b18 0%, #3d3832 100%);
            color: #fff;
            padding: 24px 28px;
            border-radius: 16px;
            min-width: 280px;
        }

        .total-box .total-label {
            font-size: 11px;
            color: #d4ccc4;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .total-box .total-amount {
            font-size: 34px;
            font-weight: 800;
            color: #ff9543;
        }

        .payment-section {
            background: #fbf5ed;
            border-radius: 16px;
            padding: 22px;
            border: 1px solid rgba(31, 27, 24, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
        }

        .payment-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .payment-icon {
            width: 52px;
            height: 52px;
            background: #ff9543;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
        }

        .payment-text {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .payment-text .payment-label {
            font-size: 10px;
            color: #8c7f74;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .payment-text .payment-method {
            font-size: 16px;
            font-weight: 800;
            color: #1f1b18;
        }

        .invoice-id {
            font-size: 10px;
            color: #8c7f74;
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #8c7f74;
            font-size: 11px;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .invoice-page {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-page">
        <div class="invoice-card">
            <div class="invoice-header-card">
                <div class="neothart-brand">
                    <h2>Neotharts</h2>
                    <div class="neothart-info">
                        <span>neothartsofficial@gmail.com</span>
                        <span>@neotharts</span>
                    </div>
                </div>
                <div class="invoice-meta">
                    <div class="invoice-label">Invoice</div>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                    <div class="invoice-date">{{ $invoice->created_at->format('d F Y, H:i') }} WIB</div>
                    <span class="status-badge {{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
                </div>
            </div>

            <h2 class="section-title">Informasi Client</h2>
            <div class="header-info-grid">
                <div class="info-group">
                    <label>Nama Client</label>
                    <span>{{ $invoice->client_name }}</span>
                </div>
                <div class="info-group">
                    <label>Email Client</label>
                    <span>{{ $invoice->client_email ?: '-' }}</span>
                </div>
                <div class="info-group">
                    <label>Instagram Client</label>
                    <span>{{ $invoice->client_instagram ?: '-' }}</span>
                </div>
            </div>

            <h2 class="section-title">Detail Service</h2>
            <div class="services-table-wrapper">
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Harga Service</th>
                            <th>Jumlah</th>
                            <th>Add Character</th>
                            <th>Kegunaan Karya</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td class="service-name">{{ $item->service_name }}</td>
                                <td>
                                    @if($invoice->currency === 'IDR')
                                        Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                    @else
                                        ${{ number_format($item->unit_price / 16000, 2, '.', ',') }}
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->additional_characters }}</td>
                                <td>
                                    <span class="usage-badge {{ $item->usage_type }}">
                                        {{ ucfirst($item->usage_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice->currency === 'IDR')
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    @else
                                        ${{ number_format($item->subtotal / 16000, 2, '.', ',') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="total-summary">
                <div class="total-box">
                    <div class="total-label">Total Pembayaran</div>
                    <div class="total-amount">
                        @if($invoice->currency === 'IDR')
                            Rp{{ number_format($invoice->total_amount, 0, ',', '.') }}
                        @else
                            ${{ number_format($invoice->total_amount / 16000, 2, '.', ',') }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="payment-section">
                <div class="payment-info">
                    <div class="payment-icon">
                        @if($invoice->payment_method === 'qris')
                            📱
                        @else
                            💳
                        @endif
                    </div>
                    <div class="payment-text">
                        <span class="payment-label">Metode Pembayaran</span>
                        <span class="payment-method">{{ $invoice->payment_method === 'qris' ? 'QRIS' : 'PayPal' }}</span>
                    </div>
                </div>
                <div class="invoice-id">Invoice ID: {{ $invoice->invoice_number }}</div>
            </div>

            <div class="footer">
                <p class="footer-text">Terima kasih telah memesan di Neotharts!</p>
            </div>
        </div>
    </div>
</body>
</html>