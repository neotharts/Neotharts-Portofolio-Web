<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'client_name',
        'client_email',
        'client_instagram',
        'payment_method',
        'currency',
        'exchange_rate',
        'status',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'exchange_rate' => 'integer',
    ];

    /**
     * Get the items for this invoice
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Generate a unique invoice number
     */
    public static function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        return "INV-{$date}-{$random}";
    }

    /**
     * Status labels
     */
    public static function getStatusLabels()
    {
        return [
            'unpaid' => 'UNPAID',
            'sketch' => 'SKETCH',
            'progress' => 'PROGRESS',
            'finishing' => 'FINISHING',
            'done' => 'DONE',
        ];
    }

    /**
     * Status colors
     */
    public static function getStatusColors()
    {
        return [
            'unpaid' => '#ef4444',
            'sketch' => '#eab308',
            'progress' => '#3b82f6',
            'finishing' => '#8b5cf6',
            'done' => '#22c55e',
        ];
    }

    /**
     * Scope for active status
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['done']);
    }
}