<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        return view('admin.gallery.index', [
            'items' => Media::orderBy('type')->orderBy('sort_order')->paginate(30),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:hero,gallery'],
            'title' => ['nullable', 'string', 'max:150'],
            'file' => ['required', 'file', 'image', 'max:4096'],
        ]);

        $path = $request->file('file')->store('media', 'public');

        if ($data['type'] === 'hero') {
            // Only one hero at a time — deactivate previous heroes.
            Media::where('type', 'hero')->update(['is_active' => false]);
        }

        Media::create([
            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'path' => $path,
            'sort_order' => (int) Media::where('type', $data['type'])->max('sort_order') + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Media uploaded.');
    }

    public function toggle(Media $media)
    {
        if ($media->type === 'hero' && $media->is_active) {
            return back()->with('error', 'Deactivate is not allowed for the active hero. Upload a new hero instead.');
        }

        $media->update(['is_active' => ! $media->is_active]);

        return back()->with('success', 'Media updated.');
    }

    public function destroy(Media $media)
    {
        Storage::disk('public')->delete($media->path);
        $media->delete();

        return back()->with('success', 'Media deleted.');
    }
}
