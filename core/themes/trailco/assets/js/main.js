/* trailco — main.js */
(function () {
  'use strict';

  // Mobile nav toggle
  var toggle = document.getElementById('tr_mobile_toggle');
  var nav    = document.getElementById('tr_mobile_nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('open');
    });
  }
})();
