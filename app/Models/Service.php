<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'starting_price',
        'type',
        'image',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'starting_price' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get features as array
     */
    public function getFeaturesArrayAttribute()
    {
        if (!$this->features) {
            return [];
        }

        return json_decode($this->features, true) ?? [];
    }

    /**
     * Set features from array
     */
    public function setFeaturesArrayAttribute($value)
    {
        $this->features = json_encode($value);
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for commission services
     */
    public function scopeCommission($query)
    {
        return $query->where('type', 'komisi');
    }

    /**
     * Scope ordered by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}