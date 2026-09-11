/* goldcraft — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.gc-nav-toggle');
  var menu   = document.querySelector('.gc-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
