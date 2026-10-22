/**
 * Product video: swaps the main product image for the portrait product video and back.
 * Used by the salesman and the public product pages (no jQuery needed).
 *
 * Markup:
 *   [data-media-scope]
 *     [data-media-frame] > [data-media-image] (main <img>) + [data-media-video] (wrapper with <video>)
 *     [data-show-video]  button that plays the video
 *     [data-show-image]  button / existing [data-thumb] gallery thumbs that go back to the photo
 *     [data-mirror-main] thumb <img> that should follow the main image src (variant changes)
 */
(function () {
  function init(scope) {
    var frame = scope.querySelector('[data-media-frame]');
    var img = frame && frame.querySelector('[data-media-image]');
    var wrap = frame && frame.querySelector('[data-media-video]');
    var video = wrap && wrap.querySelector('video');
    if (!img || !video) return;

    function markActive(btn) {
      if (!btn) return;
      scope.querySelectorAll('[data-show-video], [data-show-image], [data-thumb]').forEach(function (t) {
        t.classList.remove('active');
      });
      btn.classList.add('active');
    }

    function showVideo(btn) {
      img.style.display = 'none';
      wrap.hidden = false;
      frame.classList.add('is-video');
      markActive(btn);
      var playing = video.play();
      if (playing && playing.catch) playing.catch(function () {});
    }

    function showImage(btn) {
      video.pause();
      wrap.hidden = true;
      img.style.display = '';
      frame.classList.remove('is-video');
      markActive(btn);
    }

    scope.addEventListener('click', function (e) {
      var videoBtn = e.target.closest('[data-show-video]');
      if (videoBtn) {
        e.preventDefault();
        showVideo(videoBtn);
        return;
      }

      // [data-thumb] gallery thumbs keep their own click handler (it swaps the image src).
      var imageBtn = e.target.closest('[data-show-image], [data-thumb]');
      if (imageBtn) {
        if (imageBtn.hasAttribute('data-show-image')) e.preventDefault();
        showImage(imageBtn);
      }
    });

    // Selecting a variant changes the main image: return to the photo and keep the photo thumb in sync.
    new MutationObserver(function () {
      scope.querySelectorAll('[data-mirror-main]').forEach(function (thumb) {
        thumb.src = img.getAttribute('src');
      });
      if (!wrap.hidden) showImage(scope.querySelector('[data-show-image]'));
    }).observe(img, { attributes: true, attributeFilter: ['src'] });
  }

  document.querySelectorAll('[data-media-scope]').forEach(init);
})();
