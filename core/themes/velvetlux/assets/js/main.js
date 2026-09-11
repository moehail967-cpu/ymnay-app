/* velvetlux — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.vl-nav-toggle');
  var menu   = document.querySelector('.vl-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
