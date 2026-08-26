/* maison — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.ms-nav-toggle');
  var menu   = document.querySelector('.ms-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
