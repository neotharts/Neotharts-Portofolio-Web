<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtworkController extends Controller
{
    /**
     * Hanya admin yang bisa akses
     */
    public function __construct()
    {
        $this->middleware(AdminMiddleware::class);
    }

    /**
     * Display a listing of artworks.
     */
    public function index(Request $request)
    {
        $query = Artwork::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by form
        if ($request->filled('form')) {
            $query->byForm($request->form);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Order dan pagination
        $artworks = $query->orderByDesc('created_at')->paginate(10);

        // Get filter options
        $types = ['komisi', 'personal', 'organisasi', 'fanart'];
        $forms = ['chibi', 'headshot', 'halfbody', 'fullbody'];

        return view('admin.artworks.index', compact('artworks', 'types', 'forms'));
    }

    /**
     * Show the form for creating a new artwork.
     */
    public function create()
    {
        $types = ['komisi', 'personal', 'organisasi', 'fanart'];
        $forms = ['chibi', 'headshot', 'halfbody', 'fullbody'];

        return view('admin.artworks.create', compact('types', 'forms'));
    }

    /**
     * Store a newly created artwork.
     */
    public function store(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
            'type' => 'required|in:komisi,personal,organisasi,fanart',
            'form' => 'required|in:chibi,headshot,halfbody,fullbody',
            'is_published' => 'boolean',
        ]);

        try {
            // Upload image
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('artworks', $filename, 'public');
                $validated['image'] = $path;
            }

            // Set user_id dan published_at
            $validated['user_id'] = auth()->id();
            if ($validated['is_published']) {
                $validated['published_at'] = now();
            }

            // Create artwork
            Artwork::create($validated);

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified artwork.
     */
    public function show(Artwork $artwork)
    {
        // Hanya admin atau pemilik yang bisa lihat
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        return view('admin.artworks.show', compact('artwork'));
    }

    /**
     * Show the form for editing the specified artwork.
     */
    public function edit(Artwork $artwork)
    {
        // Hanya admin yang bisa edit
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        $types = ['komisi', 'personal', 'organisasi', 'fanart'];
        $forms = ['chibi', 'headshot', 'halfbody', 'fullbody'];

        return view('admin.artworks.edit', compact('artwork', 'types', 'forms'));
    }

    /**
     * Update the specified artwork.
     */
    public function update(Request $request, Artwork $artwork)
    {
        // Hanya admin yang bisa update
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        // Validasi
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'type' => 'required|in:komisi,personal,organisasi,fanart',
            'form' => 'required|in:chibi,headshot,halfbody,fullbody',
            'is_published' => 'boolean',
        ]);

        try {
            // Upload image baru jika ada
            if ($request->hasFile('image')) {
                // Hapus image lama
                if ($artwork->image && Storage::disk('public')->exists($artwork->image)) {
                    Storage::disk('public')->delete($artwork->image);
                }

                $file = $request->file('image');
                $filename = Str::slug($request->title) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('artworks', $filename, 'public');
                $validated['image'] = $path;
            }

            // Update published_at jika status berubah
            if ($validated['is_published'] && !$artwork->is_published) {
                $validated['published_at'] = now();
            } elseif (!$validated['is_published']) {
                $validated['published_at'] = null;
            }

            // Update artwork
            $artwork->update($validated);

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified artwork.
     */
    public function destroy(Artwork $artwork)
    {
        // Hanya admin yang bisa hapus
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin) {
            abort(403);
        }

        try {
            // Hapus image
            if ($artwork->image && Storage::disk('public')->exists($artwork->image)) {
                Storage::disk('public')->delete($artwork->image);
            }

            // Hapus artwork
            $artwork->delete();

            return redirect()->route('admin.artworks.index')
                           ->with('success', 'Artwork berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
