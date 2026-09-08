@extends('layouts.public')

@section('title', 'Gallery — Kopi Rider')

@section('content')
<section class="section-pad" style="padding-top:40px;">
  <div class="wrap">
    <div class="reveal">
      <h2 class="section-title">Gallery</h2>
      <p class="section-sub">
        Our truck hasn't hit the road yet — this gallery starts empty on purpose and
        grows with real photos from real events. No stock images, ever.
      </p>
    </div>

    <div class="gallery-grid">
      @forelse ($items as $item)
        <div class="gallery-item reveal">
          <img src="{{ Storage::url($item->path) }}" alt="{{ $item->title ?? 'Kopi Rider at an event' }}" loading="lazy" decoding="async">
        </div>
      @empty
        <div class="gallery-empty">
          <span class="big">📷</span>
          <strong>No photos yet — the truck is being built.</strong><br>
          Follow us on <a href="{{ setting('instagram_url', 'https://instagram.com') }}" style="color:#8B4226;font-weight:700;">Instagram</a>
          or <a href="{{ setting('tiktok_url', 'https://tiktok.com') }}" style="color:#8B4226;font-weight:700;">TikTok</a>
          for the first shots when we launch.
        </div>
      @endforelse
    </div>

    <p style="font-size:13px;color:#A6906F;margin-top:18px;" class="reveal">
      We keep this gallery fast: images are compressed, lazy-loaded, and we'd rather
      show eight quick photos than thirty slow ones.
    </p>
  </div>
</section>
@endsection
