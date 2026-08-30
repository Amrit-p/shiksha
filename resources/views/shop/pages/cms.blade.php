@extends('shop.theme')

@section('content')

<section class="page-hero">
  <span class="glow-orb glow-orb--pink" style="width:420px;height:420px;top:-140px;left:-100px"></span>
  <span class="glow-orb glow-orb--blue" style="width:400px;height:400px;bottom:-180px;right:-90px"></span>
  <div class="container z-1">
    <h1 data-reveal="up">{{ $page->title }}</h1>
    <div class="breadcrumb-pill" data-reveal="up" style="--reveal-delay:120ms">
      <a href="{{ route('shop.home') }}">Home</a><span class="sep">/</span><span class="current">{{ $page->title }}</span>
    </div>
  </div>
</section>

<section class="section pt-0">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="p-4 p-lg-5 rounded-2xl cms-content" data-reveal="up" style="background:#fff;border:1px solid var(--sl-line);box-shadow:var(--sl-shadow-sm)">
          {!! $page->content !!}
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
