/* ============================================================
   KOPI RIDER — shared UI behaviour (reveal on scroll, header)
   ============================================================ */
(function () {
  'use strict';

  // Reveal sections as they enter the viewport
  const observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });

    const top = document.querySelector('.top');
    if (top) {
      const onScroll = function () { top.classList.toggle('scrolled', window.scrollY > 8); };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }
  });
})();
