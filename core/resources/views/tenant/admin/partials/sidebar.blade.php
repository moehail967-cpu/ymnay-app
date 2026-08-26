<!-- Collapsed Sidebar Flyout Styles -->
<style>
    .admin-sidebar .nav>.nav-item>.nav-flyout {
        display: none;
    }

    .sidebar-collapsed .admin-sidebar .nav>.nav-item>.nav-flyout {
        display: block;
        position: fixed;
        left: 4.5rem;
        top: var(--nf-top, 0px);
        z-index: 9999;
        min-width: 195px;
        background: var(--color-bg-surface, #ffffff);
        border: 1px solid var(--color-border-main, #e0f0f0);
        border-radius: 0 8px 8px 0;
        box-shadow: 4px 6px 24px rgba(0, 0, 0, .12);
        padding: 4px 0;
        opacity: 0;
        pointer-events: none;
        transition: opacity .14s ease;
    }

    .sidebar-collapsed .admin-sidebar .nav>.nav-item.nf-open>.nav-flyout {
        opacity: 1;
        pointer-events: auto;
    }

    .nav-flyout .nf-header {
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 700;
        color: var(--color-text-dark, #111827);
        border-bottom: 1px solid var(--color-border-subtle, #edf9f9);
        white-space: nowrap;
    }

    .nav-flyout .nf-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 7px 16px;
        font-size: 13px;
        color: var(--color-text-muted, #6b7280);
        text-decoration: none;
        white-space: nowrap;
        transition: background .12s, color .12s;
    }

    .nav-flyout .nf-link:hover {
        background: var(--color-bg-muted, #edf9f9);
        color: var(--color-primary, #2d6a4f);
    }

    .nav-flyout .nf-link.nf-active {
        color: var(--color-primary, #2d6a4f);
        font-weight: 700;
    }

    .nav-flyout .nf-link.nf-solo {
        font-weight: 600;
        color: var(--color-text-dark, #111827);
    }

    .nav-flyout .nf-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
        opacity: .6;
    }

    /* Custom Scrollbar for Sidebar */
    .sidebar-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background-color: #a4e0d4;
        border-radius: 8px;
    }

    .sidebar-scroll:hover::-webkit-scrollbar-thumb {
        background-color: #8cd4c6;
    }
</style>

<!-- Editorial Sidebar -->
<aside
    class="admin-sidebar fixed left-0 top-0 h-full w-60 bg-sidebar z-40 flex flex-col transition-transform duration-300"
    id="adminSidebar">

    <!-- Sidebar Header: Logo & Brand -->
    <div class="flex items-center gap-3 px-5 pt-6 pb-5 flex-shrink-0 m-auto h-16">
        <a href="{{route('tenant.admin.dashboard')}}" class="flex items-center gap-3">
            @php
                $logo_id = get_static_option(
                    get_static_option('dark_mode_for_admin_panel') ? 'site_white_logo' : 'site_logo'
                );
                $logo_markup = render_image_markup_by_attachment_id($logo_id ?? get_static_option('site_title', __('Admin')), 'w-28 h-9 rounded-xl');
            @endphp
            <span class="sidebar-menu-text flex items-center gap-2.5">
                {!! render_image_markup_by_attachment_id(get_static_option('site_favicon'), 'w-9 h-9 rounded-xl') !!}
                <span class="block text-sm font-bold text-dark leading-tight">
                    {!! $logo_markup !!}
                </span>
            </span>
            <span class="sidebar-collapsed-logo hidden">
                {!! render_image_markup_by_attachment_id(get_static_option('site_favicon'), 'h-8 w-8 rounded-lg') !!}
            </span>
        </a>
        <button onclick="closeSidebar()"
            class="lg:hidden p-1.5 rounded-lg text-subtle hover:bg-muted transition-colors ml-auto">
            <i class="mdi mdi-close text-lg"></i>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="px-4 pt-5 pb-3 sidebar-menu-text">
        <div class="relative group">
            <i
                class="mdi mdi-magnify absolute left-3 top-1/2 -translate-y-1/2 text-lg text-slate-400 group-focus-within:text-[#21b777] transition-colors duration-200 pointer-events-none"></i>
            <input type="text" id="menuSearch"
                class="w-full pl-10 pr-[3.5rem] py-2 text-[13px] bg-white border border-slate-200/60 rounded-xl shadow-[0_2px_12px_rgba(0,0,0,0.03)] focus:outline-none focus:border-[#21b777]/30 focus:ring-4 focus:ring-[#21b777]/10 transition-all duration-200 text-slate-700 placeholder-slate-400"
                placeholder="{{__('Search menus...')}}">
        </div>
    </div>

    <!-- Scrollable Menu Area -->
    <div class="flex-1 overflow-y-auto sidebar-scroll">
        <ul class="nav">
            {!! \App\Facades\LandlordAdminMenu::render_tenant_sidebar_menus() !!}
        </ul>

        <div id="noResults" class="hidden text-center text-subtle py-8">
            <i class="mdi mdi-magnify-close text-3xl mb-2 block"></i>
            <p class="text-sm">{{__('No menu items found')}}</p>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="flex-shrink-0 pb-5 py-2 sidebar-menu-text border-t">
        <!-- Visit Website -->
        <a href="{{tenant_url_with_protocol(tenant()?->domain?->domain)}}" target="_blank"
            class="flex items-center gap-3 px-5 py-2 text-sm text-muted hover:text-dark hover:bg-muted transition-colors">
            <i class="mdi mdi-open-in-new text-lg"></i>
            <span>{{__('Visit Website')}}</span>
        </a>

        <!-- Sign Out -->
        <a href="#"
            onclick="event.preventDefault(); document.getElementById('sidebar_logout_btn').dispatchEvent(new MouseEvent('click'));"
            class="flex items-center gap-3 px-5 py-2 text-sm text-muted hover:text-danger hover:bg-danger-soft transition-colors">
            <i class="mdi mdi-logout text-lg"></i>
            <span>{{__('Sign Out')}}</span>
            <form id="sidebar-logout-form" action="{{ route('tenant.admin.logout') }}" method="POST" class="hidden">
                @csrf
                <button class="hidden" type="submit" id="sidebar_logout_btn"></button>
            </form>
        </a>
    </div>
</aside>

<!-- Collapsed Sidebar Flyout Script -->
<script>
    (function () {
        function escHtml(s) {
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function buildFlyout(navItem) {
            var trigger = navItem.querySelector(':scope > button, :scope > a');
            if (!trigger) return null;
            var labelEl = trigger.querySelector('.menu-title');
            var label = labelEl ? labelEl.textContent.trim() : '';
            if (!label) return null;

            var subItems = [];
            var collapse = navItem.querySelector(':scope > .submenu-collapse');
            if (collapse) {
                collapse.querySelectorAll('li.nav-item > a').forEach(function (a) {
                    var t = a.querySelector('.menu-title');
                    if (t && t.textContent.trim()) {
                        subItems.push({
                            href: a.getAttribute('href') || '#',
                            label: t.textContent.trim(),
                            active: a.classList.contains('font-bold')
                        });
                    }
                });
            }

            var html = '';
            if (subItems.length) {
                html += '<div class="nf-header">' + escHtml(label) + '</div>';
                subItems.forEach(function (s) {
                    html += '<a href="' + escHtml(s.href) + '" class="nf-link' + (s.active ? ' nf-active' : '') + '">'
                        + '<span class="nf-dot"></span>' + escHtml(s.label) + '</a>';
                });
            } else {
                var href = trigger.getAttribute('href');
                if (!href || href === '#') return null;
                html += '<a href="' + escHtml(href) + '" class="nf-link nf-solo">' + escHtml(label) + '</a>';
            }

            var div = document.createElement('div');
            div.className = 'nav-flyout';
            div.innerHTML = html;
            return div;
        }

        function initFlyouts() {
            var sidebar = document.getElementById('adminSidebar');
            if (!sidebar) return;

            sidebar.querySelectorAll('.nav > .nav-item').forEach(function (item) {
                var flyout = buildFlyout(item);
                if (!flyout) return;
                item.appendChild(flyout);

                function updateTop() {
                    var top = Math.round(item.getBoundingClientRect().top);
                    var flyoutH = flyout.scrollHeight || 200;
                    var maxTop = window.innerHeight - flyoutH - 8;
                    if (top > maxTop) top = maxTop;
                    if (top < 4) top = 4;
                    item.style.setProperty('--nf-top', top + 'px');
                }

                item.addEventListener('mouseenter', function () {
                    updateTop();
                    item.classList.add('nf-open');
                });

                item.addEventListener('mouseleave', function (e) {
                    var to = e.relatedTarget;
                    if (to && (to === flyout || flyout.contains(to))) return;
                    item.classList.remove('nf-open');
                });

                flyout.addEventListener('mouseleave', function () {
                    item.classList.remove('nf-open');
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFlyouts);
        } else {
            initFlyouts();
        }
    })();
</script>