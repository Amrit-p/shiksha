{{--
  Small auto-sliding carousel of trust badges (Admin > Trust Badges). $trustBadges comes from a view composer.
  $variant: 'full' (home page strip) or 'compact' (product page, under Add to Cart).
--}}
@php($variant = $variant ?? 'full')
@if ($trustBadges->isNotEmpty())
@once
<style>
  .trust-badges { clear: both; width: 100%; }
  .trust-badges--full { padding: 18px 0 6px; }
  .trust-badges--compact { margin-top: 22px; }
  .tb-slider { position: relative; margin: 0 -6px; }
  /* Before Slick starts (or without JS) still show a tidy row. */
  .tb-slider:not(.slick-initialized) { display: flex; overflow: hidden; }
  .tb-slider:not(.slick-initialized) .tb-item { flex: 0 0 25%; }
  .tb-item { padding: 0 6px; outline: none; }
  .tb-card {
    display: flex; align-items: center; gap: 12px; height: 76px; padding: 10px 14px;
    background: #fff; border: 1px solid #f1e3ee; border-radius: 12px; box-shadow: 0 3px 10px rgba(23, 16, 31, .05);
  }
  .tb-card img { width: 44px; height: 44px; flex: 0 0 44px; object-fit: contain; }
  .tb-text { min-width: 0; line-height: 1.25; }
  .tb-title { display: block; font-weight: 700; font-size: 14px; color: #17101f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .tb-sub { display: block; margin-top: 2px; font-size: 12px; color: #6b6178; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .trust-badges--compact .tb-card { height: 64px; padding: 8px 10px; gap: 8px; }
  .trust-badges--compact .tb-card img { width: 34px; height: 34px; flex-basis: 34px; }
  .trust-badges--compact .tb-title { font-size: 13px; }
  .trust-badges--compact .tb-sub { font-size: 11px; -webkit-line-clamp: 1; }
  .tb-arrow {
    position: absolute; top: 50%; z-index: 2; width: 28px; height: 28px; margin-top: -14px; padding: 0;
    border: 0; border-radius: 50%; background: #ec008c; color: #fff; font-size: 16px; line-height: 28px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .18); cursor: pointer;
  }
  .tb-arrow:hover, .tb-arrow:focus { background: #c20073; outline: none; }
  .tb-prev { left: -8px; }
  .tb-next { right: -8px; }
  @media (max-width: 767px) {
    .tb-slider:not(.slick-initialized) .tb-item { flex-basis: 50%; }
    .tb-card { height: 68px; padding: 8px 10px; gap: 8px; }
    .tb-card img { width: 34px; height: 34px; flex-basis: 34px; }
    .tb-title { font-size: 13px; }
    .tb-sub { font-size: 11px; -webkit-line-clamp: 1; }
  }
</style>
<script>
  // jQuery + Slick load at the end of the layout; they have run by DOMContentLoaded.
  document.addEventListener('DOMContentLoaded', function () {
    if (!window.jQuery || !jQuery.fn.slick) return;
    jQuery('.tb-slider').each(function () {
      var compact = this.getAttribute('data-variant') === 'compact';
      jQuery(this).slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        pauseOnHover: true,
        pauseOnFocus: true,
        infinite: true,
        dots: false,
        prevArrow: '<button type="button" class="tb-arrow tb-prev" aria-label="Previous"><span class="fa fa-angle-left"></span></button>',
        nextArrow: '<button type="button" class="tb-arrow tb-next" aria-label="Next"><span class="fa fa-angle-right"></span></button>',
        // Fewer badges than slidesToShow: Slick shows them in a row without sliding.
        responsive: compact
          ? [{ breakpoint: 1199, settings: { slidesToShow: 3 } }, { breakpoint: 991, settings: { slidesToShow: 4 } }, { breakpoint: 767, settings: { slidesToShow: 2 } }]
          : [{ breakpoint: 991, settings: { slidesToShow: 3 } }, { breakpoint: 767, settings: { slidesToShow: 2 } }]
      });
    });
  });
</script>
@endonce

<div class="trust-badges trust-badges--{{ $variant }}">
  @if ($variant === 'full')<div class="container">@endif
    <div class="tb-slider" data-variant="{{ $variant }}">
      @foreach ($trustBadges as $badge)
        <div class="tb-item">
          <div class="tb-card">
            <img src="{{ asset('storage/'.$badge->icon) }}" alt="" loading="lazy">
            <div class="tb-text">
              <span class="tb-title" title="{{ $badge->title }}">{{ $badge->title }}</span>
              @if ($badge->subtitle)<span class="tb-sub">{{ $badge->subtitle }}</span>@endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @if ($variant === 'full')</div>@endif
</div>
@endif
