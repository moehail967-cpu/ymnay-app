/* luxegems — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.lx-nav-toggle');
  var menu   = document.querySelector('.lx-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
