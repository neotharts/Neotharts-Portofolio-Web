<?php

namespace App\Http\Requests;

use App\Models\Artwork;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArtworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $artwork = $this->route('artwork');

        return (bool) $this->user()?->is_admin
            || ($artwork && $this->user()?->id === $artwork->user_id);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp'],
            'add_images' => ['nullable', 'array', 'max:12'],
            'add_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp'],
            'image_order' => ['nullable', 'string'],
            'images_to_delete' => ['nullable', 'string'],
            'type' => ['required', Rule::in($this->validTypes())],
            'form' => ['nullable', 'string'],
            'list_service' => ['nullable', 'array'],
            'list_service.*' => ['string', Rule::in($this->availableServices())],
            'art_for' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function availableServices(): array
    {
        $services = Service::active()->orderBy('sort_order')->pluck('name')->toArray();

        return !empty($services) ? $services : ['headshot', 'halfbody', 'fullbody', 'chibi'];
    }

    private function validTypes(): array
    {
        $dbTypes = Artwork::distinct()->pluck('type')->filter()->values()->toArray();
        $defaultTypes = ['komisi', 'personal', 'organisasi', 'fanart'];
        $types = !empty($dbTypes) ? array_unique(array_merge($dbTypes, $defaultTypes)) : $defaultTypes;
        sort($types);

        return $types;
    }
}
