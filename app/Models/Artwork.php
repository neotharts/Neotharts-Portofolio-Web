<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'title', 'description', 'image', 'images', 'type', 'form', 'list_service', 'art_for', 'is_published', 'sort_order', 'published_at'])]
class Artwork extends Model
{
    use HasFactory;

    /**
     * Mass assignment yang dibolehkan.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'images',
        'type',
        'form',
        'list_service',
        'art_for',
        'is_published',
        'sort_order',
        'published_at',
    ];

    /**
     * Cast values untuk atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'list_service' => 'array',
        'images' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get the user who created this artwork.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes untuk query optimization
     */

    /**
     * Filter berdasarkan status publish
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Order artwork by custom order, then newest.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('created_at');
    }

    /**
     * Filter berdasarkan tipe artwork
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Filter berdasarkan form artwork
     */
    public function scopeByForm($query, $form)
    {
        return $query->where('form', $form);
    }

    /**
     * Filter berdasarkan list_service
     */
    public function scopeByService($query, $service)
    {
        return $query->whereJsonContains('list_service', $service);
    }

    /**
     * Get list_service as array (with fallback)
     */
    public function getListServiceArrayAttribute()
    {
        return $this->list_service ?? [];
    }

    /**
     * Get all artwork images, falling back to the legacy cover image.
     */
    public function getGalleryImagesAttribute()
    {
        $images = $this->images ?? [];

        if (empty($images) && $this->image) {
            return [$this->image];
        }

        return $images;
    }

    /**
     * Filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Search artwork berdasarkan title atau description
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('title', 'like', "%{$keyword}%")
                     ->orWhere('description', 'like', "%{$keyword}%");
    }
}
