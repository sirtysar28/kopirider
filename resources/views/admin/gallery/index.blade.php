@extends('layouts.admin')

@section('title', 'Media')

@section('content')
<div class="page-head">
  <div>
    <h1>Media</h1>
    <p class="muted">
      One hero image for the landing page + a gallery built for growth.
      Upload compressed photos (max 4 MB) — they're lazy-loaded on the site.
    </p>
  </div>
</div>

<div class="card">
  <h2 style="font-size:19px;margin-bottom:14px;">Upload media</h2>
  <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="form-grid">
    @csrf
    <div class="field">
      <label for="type">Type</label>
      <select id="type" name="type">
        <option value="gallery">Gallery photo</option>
        <option value="hero">Hero image (replaces current)</option>
      </select>
    </div>
    <div class="field">
      <label for="title">Title (optional)</label>
      <input type="text" id="title" name="title" placeholder="e.g. Wedding at Batu Belig">
    </div>
    <div class="field">
      <label for="file">Image file *</label>
      <input type="file" id="file" name="file" accept="image/*" required>
    </div>
    <button type="submit" class="btn">Upload</button>
  </form>
</div>

<div class="card">
  <h2 style="font-size:19px;margin-bottom:14px;">Media library</h2>
  <div class="table-wrap table-cards">
    <table class="list">
      <thead>
        <tr><th>Preview</th><th>Type</th><th>Title</th><th>Active</th><th>Sort</th><th></th></tr>
      </thead>
      <tbody>
        @forelse ($items as $item)
          <tr>
            <td data-label="Preview"><img src="{{ Storage::url($item->path) }}" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:8px;border:1px solid #EFE3C4;"></td>
            <td data-label="Type"><span class="badge {{ $item->type === 'hero' ? 'negotiating' : 'new' }}">{{ $item->type }}</span></td>
            <td data-label="Title">{{ $item->title ?? '—' }}</td>
            <td data-label="Active"><span class="badge {{ $item->is_active ? 'confirmed' : 'lost' }}">{{ $item->is_active ? 'yes' : 'no' }}</span></td>
            <td data-label="Sort">{{ $item->sort_order }}</td>
            <td data-label="Actions" style="white-space:nowrap;">
              @if ($item->type !== 'hero')
                <form method="POST" action="{{ route('admin.gallery.toggle', $item) }}" style="display:inline;">
                  @csrf
                  <button class="btn btn-sm btn-line">{{ $item->is_active ? 'Hide' : 'Show' }}</button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}" onsubmit="return confirm('Delete this media?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">Nothing uploaded yet — the website shows clean placeholders until real photos exist.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $items->links('partials.admin-pagination') }}
</div>
@endsection
