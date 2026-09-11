/* fitpeak — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.fp-nav-toggle');
  var menu   = document.querySelector('.fp-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
