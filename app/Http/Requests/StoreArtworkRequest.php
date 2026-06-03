<?php

namespace App\Http\Requests;

use App\Models\Artwork;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArtworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'images' => ['required', 'array', 'min:1', 'max:12'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp'],
            'image_order' => ['nullable', 'string'],
            'type' => ['required', Rule::in($this->validTypes())],
            'form' => ['nullable', 'string', 'max:255'],
            'list_service' => ['nullable', 'array'],
            'list_service.*' => ['string', Rule::in($this->availableServices())],
            'art_for' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul artwork wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'images.required' => 'Minimal satu gambar wajib diupload',
            'images.array' => 'Upload gambar tidak valid',
            'images.min' => 'Minimal satu gambar wajib diupload',
            'images.max' => 'Maksimal 12 gambar per artwork',
            'images.*.image' => 'Semua file harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus JPEG, PNG, JPG, GIF, atau WebP',
            'type.required' => 'Tipe artwork wajib dipilih',
            'type.in' => 'Tipe artwork tidak valid',
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
