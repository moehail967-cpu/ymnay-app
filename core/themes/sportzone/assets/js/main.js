/* sportzone — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.sz-nav-toggle');
  var menu   = document.querySelector('.sz-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
