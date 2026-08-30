@extends('shop.layout')

@section('content')
<div class="page-wrap page-hero">
    <h1>{{ $page->title }}</h1>
</div>
<div class="page-wrap" style="padding-bottom:3rem;">
    <div class="panel" style="padding:2rem;line-height:1.8;color:var(--brand-muted);">
        {!! $page->content !!}
    </div>
</div>
@endsection
