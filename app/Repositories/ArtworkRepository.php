<?php

namespace App\Repositories;

use App\Models\Artwork;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArtworkRepository
{
    public function publishedQuery(): Builder
    {
        return Artwork::query()
            ->with(['user', 'services'])
            ->where('is_published', true);
    }

    public function homepageLatest(int $limit = 3): Collection
    {
        return $this->publishedQuery()
            ->where('form', '!=', 'chibi')
            ->whereJsonDoesntContain('list_service', 'Chibi')
            ->whereJsonDoesntContain('list_service', 'chibi')
            ->ordered()
            ->take($limit)
            ->get();
    }

    public function filteredPublished(?string $type = null, ?string $service = null): Collection
    {
        $query = $this->publishedQuery();

        if ($type) {
            $query->where('type', $type);
        }

        if ($service) {
            $query->where(function (Builder $query) use ($service) {
                $query->whereHas('services', fn (Builder $serviceQuery) => $serviceQuery->where('name', $service))
                    ->orWhereJsonContains('list_service', $service);
            });
        }

        return $query->ordered()->get();
    }

    public function latestForService(Service $service): ?Artwork
    {
        return $this->publishedQuery()
            ->where(function (Builder $query) use ($service) {
                $query->whereHas('services', fn (Builder $serviceQuery) => $serviceQuery->whereKey($service->id))
                    ->orWhereRaw('LOWER(list_service) LIKE ?', ['%' . strtolower($service->name) . '%']);
            })
            ->ordered()
            ->first();
    }

    public function publishedTypes(): array
    {
        $dbTypes = $this->publishedQuery()
            ->distinct()
            ->pluck('type')
            ->filter()
            ->values()
            ->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $types = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($types);

        return $types;
    }

    public function syncServices(Artwork $artwork, array $serviceNames): void
    {
        $normalizedNames = array_map(fn ($name) => strtolower(trim((string) $name)), $serviceNames);
        $serviceIds = Service::query()
            ->whereIn(DB::raw('LOWER(name)'), $normalizedNames)
            ->pluck('id')
            ->toArray();

        $artwork->services()->sync($serviceIds);
    }

    public function toPublicArray(Collection $artworks): array
    {
        return $artworks->map(function (Artwork $artwork) {
            return [
                'id' => $artwork->id,
                'title' => $artwork->title,
                'description' => $artwork->description,
                'image' => $artwork->image,
                'images' => $artwork->gallery_images,
                'type' => $artwork->type,
                'form' => $artwork->form,
                'list_service' => $artwork->list_service ?? $artwork->services->pluck('name')->toArray(),
                'art_for' => $artwork->art_for,
                'sort_order' => $artwork->sort_order,
                'published_at' => $artwork->published_at?->toISOString(),
                'created_at' => $artwork->created_at?->toISOString(),
            ];
        })->toArray();
    }
}
