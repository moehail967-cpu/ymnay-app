/* tinynest — main.js */
(function () {
  'use strict';
  var toggle = document.querySelector('.tn-nav-toggle');
  var menu   = document.querySelector('.tn-nav-links');
  if (toggle && menu) {
    toggle.addEventListener('click', function () { menu.classList.toggle('open'); });
  }
})();
