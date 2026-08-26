/* drivekit — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.dk-nav-toggle');
  var menu   = document.querySelector('.dk-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
