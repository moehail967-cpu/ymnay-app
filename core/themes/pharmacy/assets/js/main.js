/* pharmacy — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.ph-nav-toggle');
  var menu   = document.querySelector('.ph-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
