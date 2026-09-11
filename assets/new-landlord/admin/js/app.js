/**
 * Main Admin UI App
 */

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle Logic
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    }

    // Dropdown Logic (Generic)
    const dropdowns = document.querySelectorAll('.ui-dropdown-trigger');
    dropdowns.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            const menu = trigger.nextElementSibling;
            if (menu) {
                menu.classList.toggle('hidden');
            }
        });
    });

    // Theme Switcher (Bootstrap for future dark mode)
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('admin-theme', newTheme);
        });
    }
});
