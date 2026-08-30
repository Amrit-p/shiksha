@extends('shop.layout')

@section('content')
<div class="page-wrap page-hero">
    <h1>About Us</h1>
    <p>{{ $section->subtitle ?? 'Quality you can trust' }}</p>
</div>
<div class="page-wrap" style="padding-bottom:3rem;">
    <div class="panel" style="padding:2rem;">
        <h2 style="font-family:var(--font-display);margin-top:0;">{{ $section->title ?? 'About Shiksha' }}</h2>
        <div style="color:var(--brand-muted);line-height:1.8;font-size:1.05rem;">
            {!! nl2br(e($section->content ?? 'Shiksha delivers reliable lighting and electrical products.')) !!}
        </div>
    </div>
</div>
@endsection
