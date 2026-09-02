// ====== Submenu Toggle (for TailwindMenuWithPermission) ======
function toggleSubmenu(btn) {
    var submenu = btn.nextElementSibling;
    if (!submenu || !submenu.classList.contains('submenu-collapse')) return;
    var arrow = btn.querySelector('.submenu-arrow');
    submenu.classList.toggle('hidden');
    if (arrow) arrow.classList.toggle('rotate-180');
}

window.toggleSubmenu = toggleSubmenu;
