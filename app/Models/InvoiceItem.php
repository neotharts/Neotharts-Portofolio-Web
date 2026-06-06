<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_id',
        'service_name',
        'unit_price',
        'quantity',
        'additional_characters',
        'usage_type',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'quantity' => 'integer',
        'additional_characters' => 'integer',
        'subtotal' => 'integer',
    ];

    /**
     * Get the invoice that owns this item
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the service for this item
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Calculate subtotal for this item
     */
    public function calculateSubtotal()
    {
        // Base price = unit_price * quantity
        $basePrice = $this->unit_price * $this->quantity;

        // Additional characters: 50% of unit_price per character
        $additionalCharactersCost = $this->unit_price * 0.5 * $this->additional_characters;

        // Usage type multiplier
        $usageMultiplier = $this->usage_type === 'commercial' ? 2 : 1;

        // Total = (base + additional) * usage multiplier
        $this->subtotal = (int) round(($basePrice + $additionalCharactersCost) * $usageMultiplier);

        return $this->subtotal;
    }
}