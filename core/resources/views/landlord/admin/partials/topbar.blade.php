<!-- Editorial Topbar -->
<header class="bg-secondary border-b border-main sticky top-0 z-30">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6">

        <!-- Left: Hamburger (mobile) + Search Bar -->
        <div class="flex items-center gap-3 flex-1">
            <button onclick="toggleSidebar()"
                class="lg:hidden p-2 rounded-lg text-muted hover:bg-muted hover:text-dark transition-colors flex-shrink-0">
                <i class="mdi mdi-menu text-xl"></i>
            </button>
            <button onclick="toggleSidebarCollapse()"
                class="hidden lg:block p-2 rounded-lg text-muted hover:bg-muted hover:text-dark transition-colors flex-shrink-0"
                title="{{ __('Toggle sidebar') }}">
                <i class="mdi mdi-menu text-xl"></i>
            </button>

        </div>

        <!-- Right: Actions + Profile -->
        <div class="flex items-center gap-1.5">

            {{-- Notifications (Blog Comments) --}}
            @if(isPluginActive('Blog'))
                <div class="relative" id="notifDropdown">
                    <button onclick="toggleDropdown('notifDropdown')"
                        class="relative p-2.5 rounded-xl text-muted hover:bg-muted hover:text-dark transition-colors">
                        <i class="mdi mdi-bell-outline text-xl"></i>
                        @php
                            $comments = $new_comments->where('status', 'unread')?->count();
                        @endphp
                        @if($comments > 0)
                            <span class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full"></span>
                        @endif
                    </button>
                    <div
                        class="dropdown-panel hidden absolute right-0 mt-2 w-80 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-subtle">
                            <h6 class="text-sm font-semibold text-dark">{{ $comments . ' ' . __('Unread Comments') }}</h6>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-subtle">
                            @foreach($new_comments as $comment)
                                <a href="{{route(route_prefix() . 'admin.blog.comments.view', $comment->blog_id)}}"
                                    class="flex items-start gap-3 px-4 py-3 hover:bg-muted transition-colors">
                                    <span
                                        class="flex-shrink-0 w-8 h-8 bg-success-soft text-success rounded-full flex items-center justify-center">
                                        <i class="mdi mdi-bell text-sm"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-dark truncate">{{__('New comment on')}}
                                            <strong>{{ Str::words($comment->blog?->title, 5) }}</strong></p>
                                        <p class="text-xs text-subtle mt-0.5">
                                            {{ $comment->created_at->diffForHumans() }}
                                            @if($comment->status == 'unread')
                                                <span class="text-danger font-medium ml-1">{{__('New')}}</span>
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{route(route_prefix() . 'admin.blog')}}"
                            class="block text-center px-4 py-2.5 text-sm text-brand font-medium hover:bg-muted border-t border-subtle">
                            {{__('See All')}}
                        </a>
                    </div>
                </div>
            @endif

            {{-- Messages / Activity --}}
            <div class="relative" id="msgDropdown">
                <button onclick="toggleDropdown('msgDropdown')"
                    class="relative p-2.5 rounded-xl text-muted hover:bg-muted hover:text-dark transition-colors">
                    <i class="mdi mdi-clock-outline text-xl"></i>
                    @if($new_message)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>
                <div
                    class="dropdown-panel hidden absolute right-0 mt-2 w-80 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-subtle">
                        <h6 class="text-sm font-semibold text-dark">{{ $new_message . ' ' . __('Messages') }}</h6>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-subtle">
                        @foreach($all_messages as $message)
                            <a href="{{route(route_prefix() . 'admin.contact.message.view', $message->id)}}"
                                class="flex items-start gap-3 px-4 py-3 hover:bg-muted transition-colors">
                                <span
                                    class="flex-shrink-0 w-8 h-8 bg-blue-50 text-info rounded-full flex items-center justify-center">
                                    <i class="mdi mdi-email text-sm"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-dark truncate">{{__('Message from')}}
                                        <strong>{{ optional($message->form)->title }}</strong></p>
                                    <p class="text-xs text-subtle mt-0.5">
                                        {{ $message->created_at->diffForHumans() }}
                                        @if($message->status === 1)
                                            <span class="text-danger font-medium ml-1">{{__('New')}}</span>
                                        @endif
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{route(route_prefix() . 'admin.contact.message.all')}}"
                        class="block text-center px-4 py-2.5 text-sm text-brand font-medium hover:bg-muted border-t border-subtle">
                        {{__('See All')}}
                    </a>
                </div>
            </div>

            {{-- Divider --}}
            <div class="hidden sm:block w-px h-8 bg-main mx-1.5"></div>

            {{-- Profile Dropdown --}}
            <div class="relative" id="profileDropdownContainer">
                <button onclick="toggleDropdown('profileDropdownContainer')"
                    class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-muted transition-colors">
                    @php
                        if (auth('admin')->user()->image != null) {
                            $image_id = auth('admin')->user()->image;
                        } else {
                            $image_id = get_static_option('placeholder_image');
                        }
                    @endphp
                    <div class="hidden sm:block text-right mr-1">
                        <p class="text-sm font-semibold text-dark leading-tight">{{auth('admin')->user()->name}}</p>
                        <p class="text-[10px] font-semibold text-subtle uppercase tracking-wider">
                            {{auth('admin')->user()->username}}</p>
                    </div>
                    <div class="relative">
                        @if($image_id != null)
                            <img src="{{ get_attachment_image_by_id($image_id)['img_url'] ?? asset('assets/landlord/uploads/media-uploader/no-image.jpg') }}"
                                alt="Profile" class="w-9 h-9 rounded-full object-cover ring-2 ring-sidebar-hover">
                        @else
                            <img src="{{asset('assets/landlord/uploads/media-uploader/no-image.jpg')}}" alt="Profile"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-sidebar-hover">
                        @endif
                    </div>
                </button>
                <div
                    class="dropdown-panel hidden absolute right-0 mt-2 w-56 bg-surface rounded-xl shadow-lg border border-main overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-subtle">
                        <p class="text-sm font-semibold text-dark">{{auth('admin')->user()->name}}</p>
                        <p class="text-xs text-muted">{{auth('admin')->user()->email}}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{route('landlord.admin.tenant.activity.log')}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-pulse text-base text-success"></i>
                            {{__('Activity Log')}}
                        </a>
                        <a href="{{route('landlord.admin.edit.profile')}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-account-edit text-base text-info"></i>
                            {{__('Edit Profile')}}
                        </a>
                        <a href="{{route('landlord.admin.change.password')}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-key text-base text-warning"></i>
                            {{__('Change Password')}}
                        </a>

                        {{-- Health --}}
                        @inject('healthHelper', 'App\Helpers\SiteHealthHelper')
                        <a href="{{route('landlord.admin.health') ?? ''}}"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors
                                  {{ $healthHelper->getWarning() ? 'text-danger hover:bg-danger-soft' : 'text-subtle hover:bg-muted hover:text-dark' }}">
                            <i
                                class="mdi mdi-stethoscope text-base {{ $healthHelper->getWarning() ? 'text-danger' : 'text-success' }}"></i>
                            {{__('Site Health')}}
                        </a>

                        {{-- Visit Site --}}
                        <a href="{{route('landlord.homepage')}}" target="_blank"
                            class="flex items-center gap-2.5 px-4 py-2 text-sm text-subtle hover:bg-muted hover:text-dark transition-colors">
                            <i class="mdi mdi-open-in-new text-base text-subtle"></i>
                            {{__('Visit Site')}}
                        </a>
                    </div>
                    <div class="border-t border-subtle">
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('tenanat_logout_submit_btn').dispatchEvent(new MouseEvent('click'));"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-danger hover:bg-danger-soft transition-colors">
                            <i class="mdi mdi-logout text-base"></i>
                            {{__('Sign Out')}}
                            <form id="logout-form" action="{{ route('landlord.admin.logout') }}" method="POST"
                                class="hidden">
                                @csrf
                                <button class="hidden" type="submit" id="tenanat_logout_submit_btn"></button>
                            </form>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // Dropdown toggle
    function toggleDropdown(id) {
        const container = document.getElementById(id);
        const panel = container.querySelector('.dropdown-panel');
        const isHidden = panel.classList.contains('hidden');

        // Close all other dropdowns
        document.querySelectorAll('.dropdown-panel').forEach(p => p.classList.add('hidden'));

        if (isHidden) {
            panel.classList.remove('hidden');
        }
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown-panel').forEach(panel => {
            if (!panel.parentElement.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });
    });

    // Fullscreen toggle
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }

    // Ctrl+K focuses the search input
    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const search = document.getElementById('menuSearch');
            if (search) {
                search.focus();
                search.select();
            }
        }
    });
</script>
