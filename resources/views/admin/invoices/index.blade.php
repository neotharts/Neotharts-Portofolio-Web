@extends('admin.layout')

@section('pageTitle', 'Invoice & Progress Board')

@push('styles')
<style>
    /* Color Variables - matching project theme */
    :root {
        --inv-primary: #ff9543;
        --inv-primary-light: #ffb37a;
        --inv-primary-dark: #e67a30;
        --inv-accent: #ffb37a;
        --inv-bg: #fbf5ed;
        --inv-surface: #ffffff;
        --inv-surface-soft: #fff7ef;
        --inv-surface-strong: #f3e6d8;
        --inv-text: #1f1b18;
        --inv-text-soft: #5f5a55;
        --inv-muted: #8c7f74;
        --inv-border: rgba(31, 27, 24, 0.08);
        --inv-success: #22c55e;
        --inv-warning: #eab308;
        --inv-danger: #ef4444;
        --inv-info: #3b82f6;
        --inv-purple: #8b5cf6;
    }

    /* Main Container */
    .invoice-progress-container {
        padding: 1.5rem;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--inv-text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h1 .material-icons-outlined {
        color: var(--inv-primary);
        font-size: 2rem;
    }

    /* Tabs */
    .tabs-container {
        margin-bottom: 2rem;
    }

    .tabs {
        display: flex;
        gap: 0.5rem;
        border-bottom: 2px solid var(--inv-border);
        padding-bottom: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .tab-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        background: transparent;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--inv-muted);
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
        white-space: nowrap;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
    }

    .tab-btn:hover {
        color: var(--inv-primary);
    }

    .tab-btn.active {
        color: var(--inv-primary);
        border-bottom-color: var(--inv-primary);
    }

    .tab-btn .tab-icon {
        vertical-align: middle;
        margin-right: 0.5rem;
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ==================== GENERATE INVOICE FORM ==================== */
    .generate-invoice-section {
        background: var(--inv-surface);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid var(--inv-border);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--inv-text);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title .material-icons-outlined {
        color: var(--inv-primary);
    }

    /* Invoice Header Card */
    .invoice-header-card {
        background: linear-gradient(135deg, var(--inv-text) 0%, #3d3832 100%);
        border-radius: 16px;
        padding: 2rem;
        color: #fff;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 2rem;
    }

    .neothart-brand {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .neothart-brand h2 {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, var(--inv-primary), var(--inv-accent));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .neothart-info {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--inv-muted);
    }

    .info-item .material-icons-outlined {
        font-size: 1.1rem;
        color: var(--inv-accent);
    }

    /* Header Info Grid */
    .header-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, var(--inv-surface-soft) 0%, var(--inv-surface-soft) 100%);
        border-radius: 12px;
        border: 1px solid var(--inv-border);
    }

    .info-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--inv-text-soft);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-group label .material-icons-outlined {
        font-size: 1rem;
        color: var(--inv-primary);
    }

    .info-group input,
    .info-group select {
        padding: 0.75rem 1rem;
        border: 2px solid var(--inv-border);
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: var(--inv-surface);
    }

    .info-group input:focus,
    .info-group select:focus {
        outline: none;
        border-color: var(--inv-primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Services Table */
    .services-table-wrapper {
        overflow-x: auto;
        margin-bottom: 1.5rem;
        border-radius: 12px;
        border: 1px solid var(--inv-border);
    }

    .services-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
        background: var(--inv-surface);
    }

    .services-table thead {
        background: linear-gradient(135deg, var(--inv-primary) 0%, var(--inv-primary) 100%);
    }

    .services-table thead th {
        padding: 1rem;
        text-align: left;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--inv-surface);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .services-table tbody tr {
        border-bottom: 1px solid var(--inv-surface-soft);
        transition: background 0.2s ease;
    }

    .services-table tbody tr:hover {
        background: var(--inv-surface-soft);
    }

    .services-table tbody tr:last-child {
        border-bottom: none;
    }

    .services-table tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
    }

    /* Service Row Inputs */
    .service-row input,
    .service-row select {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--inv-border);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .service-row input:focus,
    .service-row select:focus {
        outline: none;
        border-color: var(--inv-primary);
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.1);
    }

    .service-name-display {
        font-weight: 600;
        color: var(--inv-text);
    }

    .price-display {
        font-weight: 600;
        color: var(--inv-primary);
    }

    /* Add Service Button */
    .add-service-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--inv-primary) 0%, var(--inv-primary) 100%);
        color: var(--inv-surface);
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }

    .add-service-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .add-service-btn .material-icons-outlined {
        font-size: 1.25rem;
    }

    /* Remove Row Button */
    .remove-row-btn {
        background: var(--inv-surface-soft);
        color: var(--inv-danger);
        border: none;
        padding: 0.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .remove-row-btn:hover {
        background: var(--inv-surface-soft);
        transform: scale(1.1);
    }

    /* Total Row */
    .total-row {
        background: linear-gradient(135deg, var(--inv-text) 0%, #334155 100%) !important;
        color: var(--inv-surface);
        font-weight: 700;
    }

    .total-row td {
        padding: 1.25rem 1rem;
    }

    .total-label {
        font-size: 1rem;
        letter-spacing: 0.5px;
    }

    .total-amount {
        font-size: 1.25rem;
        color: #fbbf24;
    }

    /* Payment Section */
    .payment-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid var(--inv-border);
    }

    .payment-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .payment-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--inv-text-soft);
    }

    .payment-options {
        display: flex;
        gap: 1rem;
    }

    .payment-option {
        flex: 1;
        padding: 1rem;
        border: 2px solid var(--inv-border);
        border-radius: 12px;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
        background: var(--inv-surface);
    }

    .payment-option:hover {
        border-color: var(--inv-primary);
        background: var(--inv-surface-soft);
    }

    .payment-option.selected {
        border-color: var(--inv-primary);
        background: rgba(99, 102, 241, 0.05);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .payment-option input {
        display: none;
    }

    .payment-option .payment-icon {
        font-size: 2rem;
        color: var(--inv-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .payment-option .payment-icon img {
        width: 36px;
        height: 36px;
        object-fit: contain;
    }

    .payment-option .payment-label {
        font-weight: 600;
        color: var(--inv-text);
    }

    /* Create Invoice Button */
    .create-invoice-btn {
        width: 100%;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, var(--inv-success) 0%, var(--inv-success) 100%);
        color: var(--inv-surface);
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 2rem;
    }

    .create-invoice-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(34, 197, 94, 0.3);
    }

    .create-invoice-btn .material-icons-outlined {
        font-size: 1.5rem;
    }

    /* ==================== KANBAN BOARD ==================== */
    .kanban-container {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding-bottom: 1rem;
        -webkit-overflow-scrolling: touch;
        min-height: 500px;
    }

    .kanban-board {
        flex: 0 0 calc(20% - 0.8rem);
        min-width: 240px;
        display: flex;
        flex-direction: column;
        border-radius: 16px;
        overflow: hidden;
    }

    .board-column {
        display: flex;
        flex-direction: column;
        min-height: 500px;
    }

    .column-header {
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .column-header.unpaid { background: linear-gradient(135deg, var(--inv-danger) 0%, var(--inv-danger) 100%); }
    .column-header.sketch { background: linear-gradient(135deg, var(--inv-warning) 0%, var(--inv-warning) 100%); }
    .column-header.progress { background: linear-gradient(135deg, var(--inv-info) 0%, var(--inv-primary) 100%); }
    .column-header.finishing { background: linear-gradient(135deg, var(--inv-primary) 0%, var(--inv-purple) 100%); }
    .column-header.done { background: linear-gradient(135deg, var(--inv-success) 0%, var(--inv-success) 100%); }

    .column-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--inv-surface);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .column-count {
        background: rgba(255,255,255,0.25);
        color: var(--inv-surface);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .column-cards {
        flex: 1;
        padding: 1rem;
        background: var(--inv-surface-soft);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        overflow-y: auto;
        max-height: calc(100vh - 300px);
        min-height: 400px;
    }

    /* Client Card */
    .client-card {
        background: var(--inv-surface);
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        cursor: grab;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .client-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .client-card.unpaid::before { background: var(--inv-danger); }
    .client-card.sketch::before { background: var(--inv-warning); }
    .client-card.progress::before { background: var(--inv-info); }
    .client-card.finishing::before { background: var(--inv-primary); }
    .client-card.done::before { background: var(--inv-success); }

    .client-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        border-color: var(--inv-primary);
    }

    .client-card.dragging {
        opacity: 0.5;
        transform: rotate(3deg) scale(1.05);
    }

    .client-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }

    .client-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--inv-text);
        line-height: 1.3;
    }

    .invoice-badge {
        background: var(--inv-surface-soft);
        color: var(--inv-muted);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .service-name {
        font-size: 0.85rem;
        color: var(--inv-primary);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin-bottom: 0.75rem;
    }

    .service-name .material-icons-outlined {
        font-size: 1rem;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 0.75rem;
        border-top: 1px solid var(--inv-surface-soft);
    }

    .card-amount {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--inv-text);
    }

    .card-actions {
        display: flex;
        gap: 0.5rem;
    }

    .card-action-btn {
        background: var(--inv-surface-soft);
        color: var(--inv-muted);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .card-action-btn:hover {
        background: var(--inv-primary);
        color: var(--inv-surface);
    }

    .card-action-btn.delete:hover {
        background: var(--inv-danger);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--inv-muted);
    }

    .empty-state .material-icons-outlined {
        font-size: 3rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 0.85rem;
        margin: 0;
    }

    /* Drag & Drop Visual Feedback */
    .kanban-board.drag-over .column-cards {
        background: rgba(99, 102, 241, 0.1);
        border: 2px dashed var(--inv-primary);
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: var(--inv-text);
        color: var(--inv-surface);
        padding: 1rem 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 1000;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.4s ease;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .toast.success {
        background: linear-gradient(135deg, var(--inv-success) 0%, var(--inv-success) 100%);
    }

    .toast.error {
        background: linear-gradient(135deg, var(--inv-danger) 0%, var(--inv-danger) 100%);
    }

    .toast .material-icons-outlined {
        font-size: 1.5rem;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .kanban-container {
            gap: 0.75rem;
        }

        .kanban-board {
            flex: 0 0 calc(33.333% - 0.5rem);
        }
    }

    @media (max-width: 768px) {
        .invoice-progress-container {
            padding: 1rem;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .invoice-header-card {
            flex-direction: column;
            text-align: center;
        }

        .neothart-info {
            justify-content: center;
        }

        .kanban-container {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .kanban-board {
            flex: 0 0 280px;
        }

        .header-info-grid {
            grid-template-columns: 1fr;
        }

        .generate-invoice-section {
            padding: 1rem;
        }

        .payment-options {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .kanban-board {
            flex: 0 0 260px;
        }

        .tab-btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .services-table {
            font-size: 0.85rem;
        }
    }

    /* Quantity Stepper */
    .quantity-stepper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quantity-stepper input {
        width: 60px;
        text-align: center;
        padding: 0.5rem;
    }

    .stepper-btn {
        background: var(--inv-primary);
        color: var(--inv-surface);
        border: none;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: all 0.2s ease;
    }

    .stepper-btn:hover {
        background: var(--inv-primary-dark);
    }
</style>
@endpush

@section('content')
<div class="invoice-progress-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1>
            <span class="material-icons-outlined">receipt_long</span>
            Invoice & Progress Board
        </h1>
    </div>

    <!-- Tabs -->
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('generate')">
                <span class="material-icons-outlined tab-icon">add_circle</span>
                Generate Invoice
            </button>
            <button class="tab-btn" onclick="switchTab('board')">
                <span class="material-icons-outlined tab-icon">view_kanban</span>
                Client Progress Board
            </button>
        </div>
    </div>

    <!-- Tab: Generate Invoice -->
    <div id="tab-generate" class="tab-content active">
        <form id="invoiceForm" action="{{ route('admin.invoices.store') }}" method="POST">
            @csrf
            <div class="generate-invoice-section">
                <!-- Invoice Header Card - Neotharts Branding -->
                <div class="invoice-header-card">
                    <div class="neothart-brand">
                        <h2>Neotharts</h2>
                        <div class="neothart-info">
                            <div class="info-item">
                                <span class="material-icons-outlined">mail</span>
                                <span>neothartsofficial@gmail.com</span>
                            </div>
                            <div class="info-item">
                                <span class="material-icons-outlined">camera_alt</span>
                                <span>@neotharts</span>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="section-title">
                    <span class="material-icons-outlined">person</span>
                    Informasi Client
                </h2>

                <div class="header-info-grid">
                    <div class="info-group">
                        <label>
                            <span class="material-icons-outlined">badge</span>
                            Nama Client
                        </label>
                        <input type="text" name="client_name" placeholder="Masukkan nama client" required>
                    </div>
                    <div class="info-group">
                        <label>
                            <span class="material-icons-outlined">alternate_email</span>
                            Email Client
                        </label>
                        <input type="email" name="client_email" placeholder="email@example.com">
                    </div>
                    <div class="info-group">
                        <label>
                            <span class="material-icons-outlined">camera_alt</span>
                            Instagram Client
                        </label>
                        <input type="text" name="client_instagram" placeholder="@username">
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 2rem;">
                    <span class="material-icons-outlined">list_alt</span>
                    Daftar Service
                </h2>

                <div class="services-table-wrapper">
                    <table class="services-table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Harga Service</th>
                                <th>Jumlah</th>
                                <th>Add Character</th>
                                <th>Kegunaan Karya</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="servicesTableBody">
                            <!-- Rows will be added here via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Total di luar tabel -->
                <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
                    <div style="background: linear-gradient(135deg, var(--inv-text) 0%, #3d3832 100%); color: #fff; padding: 1.25rem 2rem; border-radius: 12px; display: flex; align-items: center; gap: 1.5rem;">
                        <span style="font-size: 0.9rem; font-weight: 600; letter-spacing: 0.5px;">TOTAL KESELURUHAN</span>
                        <div style="text-align: right;">
                            <div id="grandTotalUSD" style="font-size: 1.25rem; font-weight: 700; color: var(--inv-primary);">$0.00</div>
                            <div id="grandTotalIDR" style="font-size: 1rem; font-weight: 600; color: var(--inv-accent);">Rp0</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="add-service-btn" onclick="addServiceRow()">
                    <span class="material-icons-outlined">add</span>
                    Tambah Service
                </button>

                <div class="payment-section">
                    <div class="payment-group">
                        <label>Metode Pembayaran</label>
                        <div class="payment-options">
                            <label class="payment-option selected" data-value="qris">
                                <input type="radio" name="payment_method" value="qris" checked>
                                <div class="payment-icon">
                                    <img src="{{ asset('img/qris.svg') }}" alt="QRIS" width="24" height="24">
                                </div>
                                <div class="payment-label">QRIS</div>
                            </label>
                            <label class="payment-option" data-value="paypal">
                                <input type="radio" name="payment_method" value="paypal">
                                <div class="payment-icon">
                                    <img src="{{ asset('img/paypal.svg') }}" alt="PayPal" width="36" height="36">
                                </div>
                                <div class="payment-label">PayPal</div>
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="create-invoice-btn">
                    <span class="material-icons-outlined">save</span>
                    Create Invoice
                </button>
            </div>
        </form>
    </div>

    <!-- Tab: Client Progress Board -->
    <div id="tab-board" class="tab-content">
        <div class="kanban-container" id="kanbanBoard">
            <!-- UNPAID -->
            <div class="kanban-board" data-status="unpaid">
                <div class="board-column">
                    <div class="column-header unpaid">
                        <span class="column-title">UNPAID</span>
                        <span class="column-count">{{ $invoices->where('status', 'unpaid')->count() }}</span>
                    </div>
                    <div class="column-cards" data-status="unpaid" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragleave="handleDragLeave(event)">
                        @forelse($invoices->where('status', 'unpaid') as $invoice)
                            @foreach($invoice->items as $item)
                                <div class="client-card unpaid" draggable="true" data-invoice-id="{{ $invoice->id }}" data-invoice-item-id="{{ $item->id }}" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                                    <div class="client-card-header">
                                        <span class="client-name">{{ $invoice->client_name }}</span>
                                        <span class="invoice-badge">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <div class="service-name">
                                        <span class="material-icons-outlined">brush</span>
                                        {{ $item->service_name }}
                                    </div>
                                    <div class="card-footer">
                                        @if($invoice->currency === 'IDR')
                                            <span class="card-amount">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="card-amount">${{ number_format($item->subtotal / 16000, 2, '.', ',') }}</span>
                                        @endif
                                        <div class="card-actions">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="card-action-btn" title="Lihat Detail">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">visibility</span>
                                            </a>
                                            <button type="button" class="card-action-btn delete" onclick="deleteInvoice({{ $invoice->id }})" title="Hapus">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="empty-state">
                                <span class="material-icons-outlined">inbox</span>
                                <p>Belum ada invoice</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- SKETCH -->
            <div class="kanban-board" data-status="sketch">
                <div class="board-column">
                    <div class="column-header sketch">
                        <span class="column-title">SKETCH</span>
                        <span class="column-count">{{ $invoices->where('status', 'sketch')->count() }}</span>
                    </div>
                    <div class="column-cards" data-status="sketch" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragleave="handleDragLeave(event)">
                        @forelse($invoices->where('status', 'sketch') as $invoice)
                            @foreach($invoice->items as $item)
                                <div class="client-card sketch" draggable="true" data-invoice-id="{{ $invoice->id }}" data-invoice-item-id="{{ $item->id }}" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                                    <div class="client-card-header">
                                        <span class="client-name">{{ $invoice->client_name }}</span>
                                        <span class="invoice-badge">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <div class="service-name">
                                        <span class="material-icons-outlined">brush</span>
                                        {{ $item->service_name }}
                                    </div>
                                    <div class="card-footer">
                                        @if($invoice->currency === 'IDR')
                                            <span class="card-amount">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="card-amount">${{ number_format($item->subtotal / 16000, 2, '.', ',') }}</span>
                                        @endif
                                        <div class="card-actions">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="card-action-btn" title="Lihat Detail">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">visibility</span>
                                            </a>
                                            <button type="button" class="card-action-btn delete" onclick="deleteInvoice({{ $invoice->id }})" title="Hapus">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="empty-state">
                                <span class="material-icons-outlined">inbox</span>
                                <p>Belum ada invoice</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- PROGRESS -->
            <div class="kanban-board" data-status="progress">
                <div class="board-column">
                    <div class="column-header progress">
                        <span class="column-title">PROGRESS</span>
                        <span class="column-count">{{ $invoices->where('status', 'progress')->count() }}</span>
                    </div>
                    <div class="column-cards" data-status="progress" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragleave="handleDragLeave(event)">
                        @forelse($invoices->where('status', 'progress') as $invoice)
                            @foreach($invoice->items as $item)
                                <div class="client-card progress" draggable="true" data-invoice-id="{{ $invoice->id }}" data-invoice-item-id="{{ $item->id }}" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                                    <div class="client-card-header">
                                        <span class="client-name">{{ $invoice->client_name }}</span>
                                        <span class="invoice-badge">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <div class="service-name">
                                        <span class="material-icons-outlined">brush</span>
                                        {{ $item->service_name }}
                                    </div>
                                    <div class="card-footer">
                                        @if($invoice->currency === 'IDR')
                                            <span class="card-amount">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="card-amount">${{ number_format($item->subtotal / 16000, 2, '.', ',') }}</span>
                                        @endif
                                        <div class="card-actions">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="card-action-btn" title="Lihat Detail">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">visibility</span>
                                            </a>
                                            <button type="button" class="card-action-btn delete" onclick="deleteInvoice({{ $invoice->id }})" title="Hapus">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="empty-state">
                                <span class="material-icons-outlined">inbox</span>
                                <p>Belum ada invoice</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- FINISHING -->
            <div class="kanban-board" data-status="finishing">
                <div class="board-column">
                    <div class="column-header finishing">
                        <span class="column-title">FINISHING</span>
                        <span class="column-count">{{ $invoices->where('status', 'finishing')->count() }}</span>
                    </div>
                    <div class="column-cards" data-status="finishing" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragleave="handleDragLeave(event)">
                        @forelse($invoices->where('status', 'finishing') as $invoice)
                            @foreach($invoice->items as $item)
                                <div class="client-card finishing" draggable="true" data-invoice-id="{{ $invoice->id }}" data-invoice-item-id="{{ $item->id }}" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                                    <div class="client-card-header">
                                        <span class="client-name">{{ $invoice->client_name }}</span>
                                        <span class="invoice-badge">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <div class="service-name">
                                        <span class="material-icons-outlined">brush</span>
                                        {{ $item->service_name }}
                                    </div>
                                    <div class="card-footer">
                                        @if($invoice->currency === 'IDR')
                                            <span class="card-amount">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="card-amount">${{ number_format($item->subtotal / 16000, 2, '.', ',') }}</span>
                                        @endif
                                        <div class="card-actions">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="card-action-btn" title="Lihat Detail">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">visibility</span>
                                            </a>
                                            <button type="button" class="card-action-btn delete" onclick="deleteInvoice({{ $invoice->id }})" title="Hapus">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="empty-state">
                                <span class="material-icons-outlined">inbox</span>
                                <p>Belum ada invoice</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- DONE -->
            <div class="kanban-board" data-status="done">
                <div class="board-column">
                    <div class="column-header done">
                        <span class="column-title">DONE</span>
                        <span class="column-count">{{ $invoices->where('status', 'done')->count() }}</span>
                    </div>
                    <div class="column-cards" data-status="done" ondragover="handleDragOver(event)" ondrop="handleDrop(event)" ondragleave="handleDragLeave(event)">
                        @forelse($invoices->where('status', 'done') as $invoice)
                            @foreach($invoice->items as $item)
                                <div class="client-card done" draggable="true" data-invoice-id="{{ $invoice->id }}" data-invoice-item-id="{{ $item->id }}" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                                    <div class="client-card-header">
                                        <span class="client-name">{{ $invoice->client_name }}</span>
                                        <span class="invoice-badge">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <div class="service-name">
                                        <span class="material-icons-outlined">brush</span>
                                        {{ $item->service_name }}
                                    </div>
                                    <div class="card-footer">
                                        @if($invoice->currency === 'IDR')
                                            <span class="card-amount">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        @else
                                            <span class="card-amount">${{ number_format($item->subtotal / 16000, 2, '.', ',') }}</span>
                                        @endif
                                        <div class="card-actions">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="card-action-btn" title="Lihat Detail">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">visibility</span>
                                            </a>
                                            <button type="button" class="card-action-btn delete" onclick="deleteInvoice({{ $invoice->id }})" title="Hapus">
                                                <span class="material-icons-outlined" style="font-size: 1rem;">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="empty-state">
                                <span class="material-icons-outlined">inbox</span>
                                <p>Belum ada invoice</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast">
    <span class="material-icons-outlined">check_circle</span>
    <span id="toastMessage">Status berhasil diperbarui!</span>
</div>
@endsection

@push('scripts')
<script>
    // Services data from database
    const services = @json($services);
    let rowCounter = 0;

    // Currency settings
    const USD_TO_IDR_RATE = 16000; // 1 USD = 16,000 IDR
    let currentCurrency = 'USD'; // Default currency

    // Format currency - amount passed is in USD
    function formatCurrency(amountUSD, currency = currentCurrency) {
        if (currency === 'IDR') {
            const amountIDR = Math.round(amountUSD * USD_TO_IDR_RATE);
            return 'Rp' + amountIDR.toLocaleString('id-ID');
        } else {
            return '$' + amountUSD.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }

    // Get payment method and update currency
    function getPaymentMethod() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        return selected ? selected.value : 'qris';
    }

    // Update currency based on payment method
    function updateCurrency() {
        const paymentMethod = getPaymentMethod();
        currentCurrency = paymentMethod === 'qris' ? 'IDR' : 'USD';
        calculateGrandTotal();
    }

    // Calculate subtotal for a row (DO NOT call calculateGrandTotal here to avoid infinite loop)
    function calculateRowSubtotal(rowIndex) {
        const unitPriceEl = document.querySelector(`#row-${rowIndex} .unit-price`);
        if (!unitPriceEl) return 0;

        const unitPriceUSD = parseFloat(unitPriceEl.dataset.price) || 0;
        const quantity = parseInt(document.querySelector(`#row-${rowIndex} .quantity-input`)?.value) || 1;
        const additionalChars = parseInt(document.querySelector(`#row-${rowIndex} .additional-chars`)?.value) || 0;
        const usageType = document.querySelector(`#row-${rowIndex} .usage-type`)?.value || 'personal';

        // Base price in USD
        const baseUSD = unitPriceUSD * quantity;

        // Additional characters: 50% of unit_price per character (USD)
        const additionalCharsCostUSD = unitPriceUSD * 0.5 * additionalChars;

        // Usage type multiplier
        const usageMultiplier = usageType === 'commercial' ? 2 : 1;

        // Total in USD = (base + additional) * usage multiplier
        const subtotalUSD = Math.round((baseUSD + additionalCharsCostUSD) * usageMultiplier * 100) / 100;

        const subtotalEl = document.querySelector(`#row-${rowIndex} .subtotal`);
        if (subtotalEl) {
            subtotalEl.textContent = formatCurrency(subtotalUSD, currentCurrency);
            subtotalEl.dataset.usd = subtotalUSD;
        }

        return subtotalUSD;
    }

    // Calculate grand total - amounts calculated in USD
    function calculateGrandTotal() {
        let totalUSD = 0;
        document.querySelectorAll('.service-row').forEach((row) => {
            const rowIndex = parseInt(row.id.replace('row-', ''));
            const subtotalUSD = calculateRowSubtotal(rowIndex);
            totalUSD += subtotalUSD;
        });
        document.getElementById('grandTotalUSD').textContent = formatCurrency(totalUSD, 'USD');
        document.getElementById('grandTotalIDR').textContent = formatCurrency(totalUSD, 'IDR');
    }

    // Add service row
    function addServiceRow() {
        const rowIndex = rowCounter++;
        const servicesOptions = services.map(s => `<option value="${s.id}" data-price="${s.starting_price}">${s.name}</option>`).join('');

        const rowHtml = `
            <tr class="service-row" id="row-${rowIndex}">
                <td>
                    <select name="items[${rowIndex}][service_id]" class="service-select" onchange="updateServicePrice(${rowIndex})" required>
                        <option value="">Pilih Service</option>
                        ${servicesOptions}
                    </select>
                </td>
                <td>
                    <span class="service-name-display unit-price" data-price="0">-</span>
                </td>
                <td>
                    <div class="quantity-stepper">
                        <button type="button" class="stepper-btn" onclick="changeQuantity(${rowIndex}, -1)">-</button>
                        <input type="number" name="items[${rowIndex}][quantity]" class="quantity-input" value="1" min="1" onchange="calculateRowSubtotal(${rowIndex}); calculateGrandTotal();">
                        <button type="button" class="stepper-btn" onclick="changeQuantity(${rowIndex}, 1)">+</button>
                    </div>
                </td>
                <td>
                    <div class="quantity-stepper">
                        <button type="button" class="stepper-btn" onclick="changeAdditionalChars(${rowIndex}, -1)">-</button>
                        <input type="number" name="items[${rowIndex}][additional_characters]" class="additional-chars" value="0" min="0" onchange="calculateRowSubtotal(${rowIndex}); calculateGrandTotal();">
                        <button type="button" class="stepper-btn" onclick="changeAdditionalChars(${rowIndex}, 1)">+</button>
                    </div>
                </td>
                <td>
                    <select name="items[${rowIndex}][usage_type]" class="usage-type" onchange="calculateRowSubtotal(${rowIndex}); calculateGrandTotal();">
                        <option value="personal">Personal Use</option>
                        <option value="commercial">Commercial Use</option>
                    </select>
                </td>
                <td>
                    <span class="price-display subtotal">$0.00</span>
                </td>
                <td>
                    <button type="button" class="remove-row-btn" onclick="removeServiceRow(${rowIndex})">
                        <span class="material-icons-outlined" style="font-size: 1.2rem;">delete</span>
                    </button>
                </td>
            </tr>
        `;

        document.getElementById('servicesTableBody').insertAdjacentHTML('beforeend', rowHtml);
        calculateGrandTotal();
    }

    // Update service price when service is selected
    function updateServicePrice(rowIndex) {
        const select = document.querySelector(`#row-${rowIndex} .service-select`);
        const priceSpan = document.querySelector(`#row-${rowIndex} .unit-price`);
        const selectedOption = select.options[select.selectedIndex];

        if (selectedOption.value) {
            const priceUSD = parseFloat(selectedOption.dataset.price) || 0;
            priceSpan.textContent = formatCurrency(priceUSD, currentCurrency);
            priceSpan.dataset.price = priceUSD;
        } else {
            priceSpan.textContent = '-';
            priceSpan.dataset.price = '0';
        }
        calculateRowSubtotal(rowIndex);
        calculateGrandTotal();
    }

    // Change quantity
    function changeQuantity(rowIndex, delta) {
        const input = document.querySelector(`#row-${rowIndex} .quantity-input`);
        if (!input) return;
        const newValue = Math.max(1, parseInt(input.value) + delta);
        input.value = newValue;
        calculateRowSubtotal(rowIndex);
        calculateGrandTotal();
    }

    // Change additional characters
    function changeAdditionalChars(rowIndex, delta) {
        const input = document.querySelector(`#row-${rowIndex} .additional-chars`);
        if (!input) return;
        const newValue = Math.max(0, parseInt(input.value) + delta);
        input.value = newValue;
        calculateRowSubtotal(rowIndex);
        calculateGrandTotal();
    }

    // Remove service row
    function removeServiceRow(rowIndex) {
        const row = document.getElementById(`row-${rowIndex}`);
        if (document.querySelectorAll('.service-row').length > 1) {
            row.remove();
            calculateGrandTotal();
        } else {
            showToast('Minimal harus ada 1 service!', 'error');
        }
    }

    // Tab switching
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        const tabId = tab === 'generate' ? 'tab-generate' : 'tab-board';
        document.getElementById(tabId).classList.add('active');
        event.target.closest('.tab-btn').classList.add('active');
    }

    // Payment option selection
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input').checked = true;
            updateCurrency(); // Update currency display when payment method changes
        });
    });

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        toastMessage.textContent = message;
        toast.className = `toast ${type}`;
        toast.querySelector('.material-icons-outlined').textContent = type === 'success' ? 'check_circle' : 'error';

        setTimeout(() => toast.classList.add('show'), 10);

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Drag and Drop functionality
    let draggedCard = null;
    let draggedInvoiceId = null;

    function handleDragStart(e) {
        draggedCard = e.target.closest('.client-card');
        draggedInvoiceId = draggedCard.dataset.invoiceId;
        draggedCard.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', draggedCard.dataset.invoiceId);
    }

    function handleDragEnd(e) {
        if (draggedCard) {
            draggedCard.classList.remove('dragging');
        }
        document.querySelectorAll('.kanban-board').forEach(board => {
            board.classList.remove('drag-over');
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        const board = e.target.closest('.kanban-board');
        if (board) board.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        const board = e.target.closest('.kanban-board');
        if (board && !board.contains(e.relatedTarget)) {
            board.classList.remove('drag-over');
        }
    }

    function handleDrop(e) {
        e.preventDefault();
        const columnCards = e.target.closest('.column-cards');
        if (!columnCards || !draggedCard) return;

        const newStatus = columnCards.dataset.status;
        const invoiceId = draggedCard.dataset.invoiceId;

        // Move card element
        columnCards.appendChild(draggedCard);

        // Update card class
        document.querySelectorAll('.client-card').forEach(card => {
            card.classList.remove('unpaid', 'sketch', 'progress', 'finishing', 'done');
            card.classList.add(newStatus);
        });

        // Update column counts
        updateColumnCounts();

        // Send AJAX request to update status
        updateInvoiceStatus(invoiceId, newStatus);

        document.querySelectorAll('.kanban-board').forEach(board => {
            board.classList.remove('drag-over');
        });
    }

    function updateColumnCounts() {
        const statuses = ['unpaid', 'sketch', 'progress', 'finishing', 'done'];
        statuses.forEach(status => {
            const count = document.querySelectorAll(`.column-cards[data-status="${status}"] .client-card`).length;
            const countEl = document.querySelector(`.kanban-board[data-status="${status}"] .column-count`);
            if (countEl) countEl.textContent = count;
        });
    }

    function updateInvoiceStatus(invoiceId, status) {
        fetch(`/admin/invoices/${invoiceId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Status berhasil diperbarui!');
            }
        })
        .catch(error => {
            console.error('Error updating status:', error);
            showToast('Gagal memperbarui status', 'error');
        });
    }

    // Delete invoice function
    function deleteInvoice(invoiceId) {
        if (!confirm('Apakah Anda yakin ingin menghapus invoice ini?')) {
            return;
        }

        fetch(`/admin/invoices/${invoiceId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.redirected) {
                // Remove the card from the board
                const card = document.querySelector(`.client-card[data-invoice-id="${invoiceId}"]`);
                if (card) {
                    card.remove();
                }
                updateColumnCounts();
                showToast('Invoice berhasil dihapus!');

                // Reload page after short delay to refresh data
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error deleting invoice:', error);
            showToast('Gagal menghapus invoice', 'error');
        });
    }

    // Initialize - add first row on page load
    document.addEventListener('DOMContentLoaded', function() {
        addServiceRow();

        // Form validation on submit
        const form = document.getElementById('invoiceForm');
        form.addEventListener('submit', function(e) {
            const serviceSelects = document.querySelectorAll('.service-select');
            let hasEmptyService = false;

            serviceSelects.forEach(select => {
                if (!select.value) {
                    hasEmptyService = true;
                    select.style.borderColor = 'var(--inv-danger)';
                } else {
                    select.style.borderColor = '';
                }
            });

            if (hasEmptyService) {
                e.preventDefault();
                showToast('Pilih service untuk setiap baris!', 'error');
            }
        });

        // Remove error styling when user selects a service
        document.querySelectorAll('.service-select').forEach(select => {
            select.addEventListener('change', function() {
                if (this.value) {
                    this.style.borderColor = '';
                }
            });
        });
    });
</script>
@endpush

