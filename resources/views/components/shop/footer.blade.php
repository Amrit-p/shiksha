@props([
    'settings' => [],
    'categories' => null,
    'pages' => null,
])

@php
    $categories = $categories ?? collect();
    $pages = $pages ?? collect();
    $phone = $settings['phone'] ?? null;
    $phoneHref = $phone ? preg_replace('/[^0-9+]/', '', $phone) : null;
    $email = $settings['email'] ?? null;
    $address = $settings['address'] ?? 'India';
    $brandName = $settings['name'] ?? 'Shiksha';
    $socials = array_filter([
        'facebook'  => $settings['facebook'] ?? null,
        'instagram' => $settings['instagram'] ?? null,
        'youtube'   => $settings['youtube'] ?? null,
        'linkedin'  => $settings['linkedin'] ?? null,
    ]);
@endphp

<!-- ============================ Footer ============================ -->
<footer class="site-footer">
  <div class="container position-relative" style="z-index:2">
    <div class="row g-4 g-lg-5">
      <div class="col-lg-4">
        <a class="footer-brand d-inline-block mb-3" href="{{ route('shop.home') }}">
          <img src="{{ asset('theme/img/logo.png') }}" alt="{{ $brandName }}">
        </a>
        <p class="mb-3">
          {{ $settings['description'] ?? $brandName.' supplies LED lighting, fans, geysers, MCBs, wires and electrical fittings across India — enquiry-based pricing with written quotes.' }}
        </p>
        @if(count($socials))
        <div class="footer-social">
          @foreach($socials as $network => $url)
            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}"><i class="bi bi-{{ $network }}"></i></a>
          @endforeach
        </div>
        @endif
      </div>

      <div class="col-6 col-lg-2">
        <h6>Categories</h6>
        <ul class="footer-links">
          @forelse($categories->take(5) as $category)
            <li><a href="{{ route('shop.products', ['category' => $category->slug ?? $category->id]) }}">{{ $category->name }}</a></li>
          @empty
            <li><a href="{{ route('shop.products') }}">Lighting</a></li>
          @endforelse
          <li><a href="{{ route('shop.products') }}">All products</a></li>
        </ul>
      </div>

      <div class="col-6 col-lg-2">
        <h6>Company</h6>
        <ul class="footer-links">
          <li><a href="{{ route('shop.about') }}">About Us</a></li>
          <li><a href="{{ route('shop.contact') }}">Contact</a></li>
          <li><a href="{{ route('shop.cart') }}">Send an Enquiry</a></li>
          @foreach($pages as $page)
            <li><a href="{{ route('shop.page', $page->slug) }}">{{ $page->title }}</a></li>
          @endforeach
        </ul>
      </div>

      <div class="col-lg-4">
        <h6>Get in touch</h6>
        <ul class="footer-contact">
          <li>
            <i class="bi bi-geo-alt-fill"></i>
            <span>{{ $brandName }} Studio &amp; Showroom<br>{{ $address }}</span>
          </li>
          @if($phone)
          <li>
            <i class="bi bi-telephone-fill"></i>
            <span><a href="tel:{{ $phoneHref }}">{{ $phone }}</a><br>
            <small class="opacity-75">Mon–Sat, 10am – 7pm IST</small></span>
          </li>
          @endif
          @if($email)
          <li>
            <i class="bi bi-envelope-fill"></i>
            <span><a href="mailto:{{ $email }}">{{ $email }}</a><br>
            <small class="opacity-75">We reply within one business day</small></span>
          </li>
          @endif
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="row align-items-center g-3">
        <div class="col-md-6">
          <p class="mb-0">© {{ date('Y') }} {{ $brandName }}®. All rights reserved.
            @foreach($pages->take(2) as $page)
              <a href="{{ route('shop.page', $page->slug) }}" class="ms-2">{{ $page->title }}</a> @if(!$loop->last)·@endif
            @endforeach
          </p>
        </div>
        <div class="col-md-6">
          <div class="pay-row justify-content-md-end">
            <span class="pay-chip">GST Registered</span>
            <span class="pay-chip">Bulk Supply</span>
            <span class="pay-chip">Trade Pricing</span>
            <span class="pay-chip">Pan-India Dispatch</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<button id="back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>
