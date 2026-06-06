<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Hanya admin yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(AdminMiddleware::class);
    }

    /**
     * Tampilkan halaman utama dengan tabs
     */
    public function index()
    {
        $services = Service::active()->ordered()->get();
        $invoices = Invoice::with('items.service')->orderBy('created_at', 'desc')->get();

        return view('admin.invoices.index', [
            'services' => $services,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Tampilkan form create invoice
     */
    public function create()
    {
        $services = Service::active()->ordered()->get();

        return view('admin.invoices.create', [
            'services' => $services,
        ]);
    }

    /**
     * Simpan invoice baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_instagram' => 'nullable|string|max:255',
            'payment_method' => 'required|in:qris,paypal',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.additional_characters' => 'nullable|integer|min:0',
            'items.*.usage_type' => 'required|in:personal,commercial',
        ]);

        DB::beginTransaction();
        try {
            // Determine currency based on payment method
            $currency = $request->payment_method === 'qris' ? 'IDR' : 'USD';
            $exchangeRate = $currency === 'IDR' ? 16000 : 1;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_instagram' => $request->client_instagram,
                'payment_method' => $request->payment_method,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'status' => 'unpaid',
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;

            // Create invoice items
            foreach ($request->items as $itemData) {
                $service = Service::find($itemData['service_id']);

                $item = new InvoiceItem();
                $item->invoice_id = $invoice->id;
                $item->service_id = $service->id;
                $item->service_name = $service->name;
                $item->unit_price = $service->starting_price;
                $item->quantity = $itemData['quantity'];
                $item->additional_characters = $itemData['additional_characters'] ?? 0;
                $item->usage_type = $itemData['usage_type'];
                $item->calculateSubtotal();
                $item->save();

                $totalAmount += $item->subtotal;
            }

            // Update total amount
            $invoice->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice berhasil dibuat dan ditambahkan ke Progress Board!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan detail invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('items.service');

        return view('admin.invoices.show', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Download invoice sebagai PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load('items.service');

        Pdf::setOptions(["isRemoteEnabled" => true]);

        $pdf = Pdf::loadView('admin.invoices.pdf', [
            'invoice' => $invoice,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Update status invoice (untuk drag & drop)
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate([
            'status' => 'required|in:unpaid,sketch,progress,finishing,done',
        ]);

        $invoice->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $invoice->status,
        ]);
    }

    /**
     * Hapus invoice
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus'
            ]);
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice berhasil dihapus');
    }
}