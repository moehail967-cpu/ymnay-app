/* bakerco — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.bk-nav-toggle');
  var menu   = document.querySelector('.bk-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
