// Mobile Menu Functionality
const menuBtn = document.getElementById('menuBtn');
const backdrop = document.getElementById('backdrop');

function getSidebar() {
    return document.getElementById('dashboardSidebar');
}

function openSidebar() {
    const sidebar = getSidebar();
    if (!sidebar || !backdrop) return;
    sidebar.classList.remove('hidden');
    backdrop.classList.remove('hidden');
}

function closeSidebar() {
    const sidebar = getSidebar();
    if (!sidebar || !backdrop) return;
    sidebar.classList.add('hidden');
    backdrop.classList.add('hidden');
}

if (menuBtn) {
    menuBtn.addEventListener('click', openSidebar);
}

if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
}

document.addEventListener('click', function (event) {
    if (event.target.closest('#btnSideberClose')) {
        closeSidebar();
    }
});