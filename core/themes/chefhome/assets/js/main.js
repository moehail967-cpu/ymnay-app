/* chefhome — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.ch-nav-toggle');
  var menu   = document.querySelector('.ch-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
