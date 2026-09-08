@extends('layouts.admin')

@section('title', 'Packages')

@section('content')
<div class="page-head">
  <div>
    <h1>Packages</h1>
    <p class="muted">Starting prices are shown publicly before anyone has to message us.</p>
  </div>
  <div class="actions">
    <a href="{{ route('admin.packages.create') }}" class="btn">+ New package</a>
  </div>
</div>

<div class="card">
  <div class="table-wrap table-cards">
    <table class="list">
      <thead>
        <tr><th>Name</th><th>Audience</th><th>Starting price</th><th>Features</th><th>Active</th><th>Sort</th><th></th></tr>
      </thead>
      <tbody>
        @forelse ($packages as $package)
          <tr>
            <td data-label="Name" style="font-weight:700;">{{ $package->name }}</td>
            <td data-label="Audience"><span class="badge {{ $package->audience === 'free' ? 'contacted' : 'new' }}">{{ $package->audience }}</span></td>
            <td data-label="Price">{{ $package->formatted_price }}</td>
            <td data-label="Features" class="muted">{{ count($package->features_list) }} items</td>
            <td data-label="Active"><span class="badge {{ $package->is_active ? 'confirmed' : 'lost' }}">{{ $package->is_active ? 'yes' : 'no' }}</span></td>
            <td data-label="Sort">{{ $package->sort_order }}</td>
            <td data-label="Actions" style="white-space:nowrap;">
              <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-line">Edit</a>
              <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No packages yet — create your first one.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $packages->links('partials.admin-pagination') }}
</div>
@endsection
