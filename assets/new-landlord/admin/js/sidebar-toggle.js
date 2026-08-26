// ====== Sidebar Toggle Functions ======
function toggleSidebar() {
    document.body.classList.toggle('sidebar-mobile-open');
}

function closeSidebar() {
    document.body.classList.remove('sidebar-mobile-open');
}

function toggleSidebarCollapse() {
    document.body.classList.toggle('sidebar-collapsed');
    var content = document.querySelector('.admin-content');
    if (document.body.classList.contains('sidebar-collapsed')) {
        content.style.marginLeft = '4.5rem';
    } else {
        content.style.marginLeft = '';
    }
}

// Ensure these are globally accessible if needed by Blade onclick handlers
window.toggleSidebar = toggleSidebar;
window.closeSidebar = closeSidebar;
window.toggleSidebarCollapse = toggleSidebarCollapse;
