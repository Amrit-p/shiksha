{{-- Animated Shiksha mark with the wordmark underneath. Used in every panel's navbar. --}}
@props(['size' => 48, 'word' => 'SHIKSHA'])

@once
<style>
  .sk-brand-mark {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    line-height: 1;
    color: inherit;
  }

  .sk-brand-mark img {
    display: block;
    width: auto;
    max-width: none;
    transform: none !important;
  }

  .sk-brand-mark__word {
    font-family: "Outfit", "Raleway", "Segoe UI", sans-serif;
    font-weight: 600;
    letter-spacing: .32em;
    padding-left: .32em;
    text-transform: uppercase;
    white-space: nowrap;
  }
</style>
@endonce

<span {{ $attributes->merge(['class' => 'sk-brand-mark']) }}>
  <picture>
    <source srcset="{{ asset('img/brand/logo-mark.png') }}" media="(prefers-reduced-motion: reduce)">
    <img src="{{ asset('img/brand/logo-animated.webp') }}" alt="{{ $word }}" style="height: {{ $size }}px;">
  </picture>
  @if ($word)
    <span class="sk-brand-mark__word" style="font-size: {{ max(8, round($size * 0.2)) }}px;">{{ $word }}</span>
  @endif
</span>
