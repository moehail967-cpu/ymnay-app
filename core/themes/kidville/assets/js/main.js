/* kidville — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.kv-nav-toggle');
  var menu   = document.querySelector('.kv-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
