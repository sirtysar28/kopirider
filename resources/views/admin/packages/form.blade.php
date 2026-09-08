@extends('layouts.admin')

@section('title', isset($package) && $package->exists ? 'Edit package' : 'New package')

@section('content')
<div class="breadcrumb"><a href="{{ route('admin.packages.index') }}">← All packages</a></div>
<div class="page-head">
  <h1>{{ isset($package) && $package->exists ? 'Edit '.$package->name : 'Create a package' }}</h1>
</div>

<div class="card" style="max-width:720px;">
  <form method="POST" action="{{ isset($package) && $package->exists ? route('admin.packages.update', $package) : route('admin.packages.store') }}">
    @csrf
    @if (isset($package) && $package->exists)
      @method('PUT')
    @endif

    <div class="form-grid">
      <div class="field">
        <label for="name">Name *</label>
        <input type="text" id="name" name="name" required value="{{ old('name', $package->name ?? '') }}" placeholder="e.g. Essential">
      </div>
      <div class="field">
        <label for="audience">Audience</label>
        <select id="audience" name="audience">
          <option value="paid" {{ old('audience', $package->audience ?? 'paid') === 'paid' ? 'selected' : '' }}>Paid events (show price)</option>
          <option value="free" {{ old('audience', $package->audience ?? '') === 'free' ? 'selected' : '' }}>Free events</option>
        </select>
      </div>
      <div class="field">
        <label for="starting_price">Starting price *</label>
        <input type="number" id="starting_price" name="starting_price" min="0" step="50000" value="{{ old('starting_price', $package->starting_price ?? 0) }}">
      </div>
      <div class="field">
        <label for="currency">Currency</label>
        <select id="currency" name="currency">
          <option value="IDR" {{ old('currency', $package->currency ?? 'IDR') === 'IDR' ? 'selected' : '' }}>IDR (Rp)</option>
          <option value="USD" {{ old('currency', $package->currency ?? '') === 'USD' ? 'selected' : '' }}>USD ($)</option>
        </select>
      </div>
      <div class="field">
        <label for="sort_order">Sort order</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order ?? 0) }}">
      </div>
    </div>

    <div class="field" style="margin-top:16px;">
      <label for="description">Short description</label>
      <textarea id="description" name="description" placeholder="One or two sentences shown under the package name.">{{ old('description', $package->description ?? '') }}</textarea>
    </div>

    <div class="field" style="margin-top:16px;">
      <label for="features">Features — one per line (shown as ✅ items)</label>
      <textarea id="features" name="features" rows="6" placeholder="Full truck + two baristas&#10;Unlimited coffee for all guests&#10;Setup &amp; cleanup included">{{ old('features', isset($package) && $package->features ? implode("\n", $package->features_list) : '') }}</textarea>
    </div>

    <label class="check-toggle" style="margin:16px 0 4px;">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
      Active (visible on the website)
    </label>

    <div style="margin-top:18px;display:flex;gap:10px;">
      <button type="submit" class="btn">{{ isset($package) && $package->exists ? 'Save changes' : 'Create package' }}</button>
      <a href="{{ route('admin.packages.index') }}" class="btn btn-line">Cancel</a>
    </div>
  </form>
</div>
@endsection
