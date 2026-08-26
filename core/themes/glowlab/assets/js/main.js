/* glowlab — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.gl-nav-toggle');
  var menu   = document.querySelector('.gl-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
