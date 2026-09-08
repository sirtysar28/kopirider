@extends('layouts.public')

@section('title', $pageTitle.' — Kopi Rider')

@section('content')
<section class="section-pad" style="padding-top:44px;">
  <div class="wrap">
    <div class="reveal legal-page">
      <h2 class="section-title">{{ $pageTitle }}</h2>
      <p class="section-sub">Kopi Rider — Coffee Truck · Bali</p>

      <div class="legal-content">
        {!! $content !!}
      </div>

      <a href="{{ route('home') }}" class="legal-back">← Back to the website</a>
    </div>
  </div>
</section>
@endsection
