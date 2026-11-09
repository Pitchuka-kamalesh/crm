    <!-- Immediate Theme Preloader (Prevents Theme Flash) -->
<script>
    (function(){
        document.documentElement.setAttribute('data-theme-color', localStorage.getItem('crm_theme_color') || 'red');
        document.documentElement.setAttribute('data-theme-mode', localStorage.getItem('crm_theme_mode') || 'light');
        if (localStorage.getItem('crm_sidebar_collapsed') === '1') {
            document.documentElement.classList.add('sidebar-is-collapsed');
        }
    })();
</script>

<!-- EXACT LMS/CRM PORTAL DARK SIDEBAR (MATCHING USER SCREENSHOT) -->
<aside class="app-sidebar" id="appSidebar">
    <!-- 1. Top Brand Header -->
    <div class="sidebar-header">
        <a href="{{ route('crm.alias') }}" class="brand-wrapper">
            <div class="brand-emblem">
                <img src="/images/vignan-shield-logo.png" alt="Vignan Emblem" class="brand-logo-img">
            </div>
            <span class="brand-title">CRM PORTAL</span>
        </a>

        <!-- Hamburger Icon (Matching Screenshot Right Corner) -->
        <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebarMenu()" title="Toggle Sidebar">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" y1="6" x2="20" y2="6"/>
                <line x1="4" y1="12" x2="20" y2="12"/>
                <line x1="4" y1="18" x2="20" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- 2. Active User Status Bar -->
    <div class="sidebar-user-strip">
        <div class="user-strip-info">
            <span class="user-status-dot"></span>
            <span class="user-strip-name" title="{{ $user->name ?? 'User' }}">{{ $user->name ?? 'User' }}</span>
        </div>
        <span class="user-strip-role-badge">
            @if($roleId == 3) DEAN
            @elseif($roleId == 2) INCHARGE
            @elseif($roleId == 4) TELE CALLER
            @elseif($roleId == 1) EMPLOYEE
            @else STAFF
            @endif
        </span>
    </div>

    <!-- 3. Navigation Menu List -->
    <div class="sidebar-menu-container">
        @php
            $currentTab = $activeTab ?? request()->query('tab', '');
            $currentPath = trim(request()->path(), '/');

            $isDashboardActive = ($currentTab === 'overview' || empty($currentTab)) 
                && !request()->routeIs('leads.*') 
                && !request()->routeIs('finance.*') 
                && !request()->routeIs('sms.*')
                && !request()->routeIs('kpi.*')
                && !request()->routeIs('crm.std*');
            if ($roleId == 4) {
                $isDashboardActive = request()->routeIs('leads.allotted') && empty($currentTab);
            }

            $roleMenus = $roleMenus ?? \App\Models\MenuAccessRole::where('role_id', $roleId)->with('menu')->orderBy('morder')->get();

            // Strictly group menus into Main (mposition = 1) and Sub (mposition = 2)
            $menuGroups = [];
            $currentMainId = null;

            foreach ($roleMenus as $access) {
                $menu = $access->menu;
                if (!$menu) continue;

                if ((int)$access->mposition === 1) {
                    $currentMainId = $menu->menu_id;
                    $menuGroups[$currentMainId] = [
                        'main' => $menu,
                        'access' => $access,
                        'submenus' => [],
                    ];
                } elseif ((int)$access->mposition === 2) {
                    $parentId = $menu->parent_menu_id ?: $currentMainId;
                    if ($parentId && isset($menuGroups[$parentId])) {
                        $menuGroups[$parentId]['submenus'][] = [
                            'menu' => $menu,
                            'access' => $access,
                        ];
                    } else {
                        $keys = array_keys($menuGroups);
                        if (!empty($keys)) {
                            $lastKey = end($keys);
                            $menuGroups[$lastKey]['submenus'][] = [
                                'menu' => $menu,
                                'access' => $access,
                            ];
                        }
                    }
                }
            }

            $resolveMenuUrl = function($controllerRef) {
                if (str_starts_with($controllerRef, 'dashboards?step=')) {
                    $step = (int)str_replace('dashboards?step=', '', $controllerRef);
                    return route('dashboards.index', ['step' => $step]);
                }
                return match($controllerRef) {
                    'crm/stddata' => route('crm.stddata', ['tab' => 'stddata']),
                    'crm/stdsearch' => route('crm.stdsearch', ['tab' => 'stdsearch']),
                    'crm/index' => route('crm.alias', ['tab' => 'overview']),
                    'leads/allottedleads' => route('leads.allotted'),
                    'leads/leadsallocation' => route('leads.allocation', ['tab' => 'allocation']),
                    'leads/index' => route('leads.index'),
                    'finance/paiddetails' => route('finance.paiddetails', ['tab' => 'paiddetails']),
                    'finance/payfee' => route('dashboards.index', ['step' => 1]),
                    'finance/index' => route('finance.index'),
                    'kpi/studentkpi' => route('kpi.studentkpi', ['tab' => 'studentkpi']),
                    'kpi/index' => route('kpi.index', ['tab' => 'kpi']),
                    'sms/index' => route('sms.index'),
                    default => url('/' . ltrim($controllerRef, '/')),
                };
            };

            $isSubActiveCheck = function($controllerRef) use ($currentPath, $currentTab) {
                return match($controllerRef) {
                    'crm/stddata' => $currentTab === 'stddata' || request()->routeIs('crm.stddata') || $currentPath === 'crm/stddata',
                    'crm/stdsearch' => $currentTab === 'stdsearch' || request()->routeIs('crm.stdsearch') || $currentPath === 'crm/stdsearch',
                    'leads/allottedleads' => request()->routeIs('leads.allotted') || str_contains($currentPath, 'allotted'),
                    'leads/leadsallocation' => request()->routeIs('leads.allocation') || str_contains($currentPath, 'allocation'),
                    'finance/paiddetails' => request()->routeIs('finance.paiddetails') || str_contains($currentPath, 'paiddetails'),
                    'finance/payfee' => str_contains($currentPath, 'payfee'),
                    'kpi/studentkpi' => request()->routeIs('kpi.studentkpi') || $currentTab === 'studentkpi' || str_contains($currentPath, 'studentkpi'),
                    default => $currentPath === trim($controllerRef, '/'),
                };
            };

            $getMenuSvgIcon = function($iconName, $menuCode) {
                if (str_starts_with($menuCode, 'DASH')) {
                    return '<svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
                }
                if (str_starts_with($menuCode, 'CRM')) {
                    return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>';
                }
                if (str_starts_with($menuCode, 'LEADS')) {
                    return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="6"/><circle cx="12" cy="2"/></svg>';
                }
                if (str_starts_with($menuCode, 'FINA')) {
                    return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="3" y1="21" x2="21" y2="21"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>';
                }
                if (str_starts_with($menuCode, 'KPI')) {
                    return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
                }
                if (str_starts_with($menuCode, 'SMS')) {
                    return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
                }
                return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9"/></svg>';
            };
        @endphp

        <!-- DYNAMIC ROLE-BASED MENUS (100% from database master.menus & master.menu_access_roles) -->
        @foreach($menuGroups as $mainId => $group)
            @php
                $main = $group['main'];
                $hasSubmenus = !empty($group['submenus']);
                $isGroupOpen = false;
                if ($hasSubmenus) {
                    foreach ($group['submenus'] as $subItem) {
                        if ($isSubActiveCheck($subItem['menu']->controller_ref)) {
                            $isGroupOpen = true;
                            break;
                        }
                    }
                } else {
                    $isGroupOpen = ($currentPath === trim($main->controller_ref, '/'));
                }
                $mainUrl = $resolveMenuUrl($main->controller_ref);
            @endphp

            @if($main->menu_code === 'DASH0001' || strtolower($main->menu_name) === 'dashboard')
                <!-- PROMINENT ACTIVE PILL: DASHBOARD (DRIVEN DYNAMICALLY FROM DATABASE) -->
                <a href="{{ $mainUrl }}" class="menu-btn-active-dashboard {{ $isDashboardActive ? 'active' : '' }}" id="nav-btn-dashboard" onclick="selectDashboardNav(event)" title="{{ $main->menu_name }}">
                    <svg viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    <span>{{ $main->menu_name }}</span>
                </a>
            @elseif($hasSubmenus)
                <!-- ACCORDION MAIN MENU (mposition = 1 with submenus mposition = 2) -->
                <div class="accordion-group {{ $isGroupOpen ? 'open' : '' }}" id="group-{{ strtolower($main->menu_code) }}">
                    <button type="button" class="accordion-header {{ $isGroupOpen ? 'active' : '' }}" onclick="toggleAccordion('group-{{ strtolower($main->menu_code) }}')">
                        <div class="header-left">
                            {!! $getMenuSvgIcon($main->icon_name, $main->menu_code) !!}
                            <span>{{ $main->menu_name }}</span>
                        </div>
                        <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="accordion-body">
                        @foreach($group['submenus'] as $subItem)
                            @php
                                $sub = $subItem['menu'];
                                $subUrl = $resolveMenuUrl($sub->controller_ref);
                                $isSubActive = $isSubActiveCheck($sub->controller_ref);
                            @endphp
                            <a href="{{ $subUrl }}" class="submenu-link {{ $isSubActive ? 'active' : '' }}">
                                {{ $sub->menu_name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- DIRECT MAIN MENU LINK (mposition = 1 without submenus) -->
                @php
                    $isMainActive = ($currentPath === trim($main->controller_ref, '/'));
                @endphp
                <div class="accordion-group {{ $isMainActive ? 'open' : '' }}" id="group-{{ strtolower($main->menu_code) }}">
                    <a href="{{ $mainUrl }}" class="accordion-header {{ $isMainActive ? 'active' : '' }}" style="text-decoration: none;">
                        <div class="header-left">
                            {!! $getMenuSvgIcon($main->icon_name, $main->menu_code) !!}
                            <span>{{ $main->menu_name }}</span>
                        </div>
                    </a>
                </div>
            @endif

            <div class="sidebar-divider"></div>
        @endforeach
    </div>

    <!-- 4. Sidebar Footer (Appearance & Theme Controls - Matching Reference Image) -->
    <div class="sidebar-footer">
        <div class="theme-section-title">APPEARANCE &amp; THEME</div>
        <div class="theme-controls-capsule">
            <!-- 4 Color Dots -->
            <div class="theme-color-palette" role="radiogroup" aria-label="Color Palette">
                <button type="button" class="theme-color-dot dot-red is-active" data-color="red" onclick="setAppThemeColor('red')" title="Red Theme" aria-label="Red Theme">
                    <span class="theme-dot-check">✓</span>
                </button>
                <button type="button" class="theme-color-dot dot-blue" data-color="blue" onclick="setAppThemeColor('blue')" title="Blue Theme" aria-label="Blue Theme">
                    <span class="theme-dot-check">✓</span>
                </button>
                <button type="button" class="theme-color-dot dot-green" data-color="green" onclick="setAppThemeColor('green')" title="Green Theme" aria-label="Green Theme">
                    <span class="theme-dot-check">✓</span>
                </button>
                <button type="button" class="theme-color-dot dot-purple" data-color="purple" onclick="setAppThemeColor('purple')" title="Purple Theme" aria-label="Purple Theme">
                    <span class="theme-dot-check">✓</span>
                </button>
            </div>

            <!-- Divider Line -->
            <div class="theme-capsule-divider"></div>

            <!-- Light / Dark Mode Toggle -->
            <button type="button" class="theme-mode-toggle" id="themeModeToggleBtn" onclick="toggleAppThemeMode()" title="Toggle Light / Dark Mode" aria-label="Toggle Light / Dark Mode">
                <div class="theme-mode-knob">
                    <svg class="mode-icon-sun" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="4" fill="#f59e0b"></circle>
                        <path d="M12 2v2"></path>
                        <path d="M12 20v2"></path>
                        <path d="m4.93 4.93 1.41 1.41"></path>
                        <path d="m17.66 17.66 1.41 1.41"></path>
                        <path d="M2 12h2"></path>
                        <path d="M20 12h2"></path>
                        <path d="m6.34 17.66-1.41 1.41"></path>
                        <path d="m19.07 4.93-1.41 1.41"></path>
                    </svg>
                    <svg class="mode-icon-moon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" fill="#93c5fd"></path>
                    </svg>
                </div>
            </button>
        </div>
    </div>
</aside>

<!-- Interactive Accordion, Sidebar Toggle & Theme Management Scripts -->
<script>
    function toggleAccordion(groupId) {
        const targetGroup = document.getElementById(groupId);
        if (!targetGroup) return;

        const isAlreadyOpen = targetGroup.classList.contains('open');

        // 1. Clear active from all headers and dashboard button
        document.querySelectorAll('.app-sidebar .accordion-header').forEach(h => {
            h.classList.remove('active');
        });
        const dashboardBtn = document.getElementById('nav-btn-dashboard');
        if (dashboardBtn) {
            dashboardBtn.classList.remove('active');
        }

        // 2. Clear active from all submenu links
        document.querySelectorAll('.app-sidebar .submenu-link').forEach(link => {
            link.classList.remove('active');
        });

        // 3. Close all groups
        document.querySelectorAll('.app-sidebar .accordion-group').forEach(group => {
            group.classList.remove('open');
        });

        // 4. Open ONLY targeted group & make ONLY its header active & glow
        if (!isAlreadyOpen) {
            targetGroup.classList.add('open');
            const header = targetGroup.querySelector('.accordion-header');
            if (header) {
                header.classList.add('active');
            }
        }
    }

    function selectDashboardNav(event) {
        // Clear active from all accordion headers & close all accordion groups
        document.querySelectorAll('.app-sidebar .accordion-header').forEach(h => {
            h.classList.remove('active');
        });
        document.querySelectorAll('.app-sidebar .submenu-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelectorAll('.app-sidebar .accordion-group').forEach(group => {
            group.classList.remove('open');
        });

        // ONLY dashboard glows
        const dashboardBtn = document.getElementById('nav-btn-dashboard');
        if (dashboardBtn) {
            dashboardBtn.classList.add('active');
        }
    }

    // Top 3 lines hamburger toggle: smoothly closes/opens sidebar
    window.toggleSidebarMenu = function() {
        const sidebar = document.getElementById('appSidebar');
        if (!sidebar) return;
        const isCollapsed = sidebar.classList.toggle('collapsed');
        document.documentElement.classList.toggle('sidebar-is-collapsed', isCollapsed);
        document.body.classList.toggle('sidebar-is-collapsed', isCollapsed);
        localStorage.setItem('crm_sidebar_collapsed', isCollapsed ? '1' : '0');
    };

    // Submenu click listener: only clicked submenu link glows, rest do not
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.app-sidebar .submenu-link');
        if (link) {
            document.querySelectorAll('.app-sidebar .submenu-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.app-sidebar .accordion-header').forEach(h => h.classList.remove('active'));
            const dashboardBtn = document.getElementById('nav-btn-dashboard');
            if (dashboardBtn) dashboardBtn.classList.remove('active');

            link.classList.add('active');
            // Keep parent accordion open
            const parentGroup = link.closest('.accordion-group');
            if (parentGroup) {
                parentGroup.classList.add('open');
            }
        }
    });

    // Appearance & Theme Management
    (function() {
        const DEFAULT_COLOR = 'red';
        const DEFAULT_MODE = 'light';

        function applyThemePreferences() {
            const color = localStorage.getItem('crm_theme_color') || DEFAULT_COLOR;
            const mode = localStorage.getItem('crm_theme_mode') || DEFAULT_MODE;
            
            document.documentElement.setAttribute('data-theme-color', color);
            document.documentElement.setAttribute('data-theme-mode', mode);

            // Update color dots UI
            document.querySelectorAll('.theme-color-dot').forEach(dot => {
                if (dot.getAttribute('data-color') === color) {
                    dot.classList.add('is-active');
                } else {
                    dot.classList.remove('is-active');
                }
            });

            // Update mode toggle UI
            const toggleBtn = document.getElementById('themeModeToggleBtn');
            if (toggleBtn) {
                if (mode === 'dark') {
                    toggleBtn.classList.add('is-dark');
                } else {
                    toggleBtn.classList.remove('is-dark');
                }
            }
        }

        window.setAppThemeColor = function(color) {
            localStorage.setItem('crm_theme_color', color);
            applyThemePreferences();
        };

        window.setAppThemeMode = function(mode) {
            localStorage.setItem('crm_theme_mode', mode);
            applyThemePreferences();
        };

        window.toggleAppThemeMode = function() {
            const currentMode = document.documentElement.getAttribute('data-theme-mode') || DEFAULT_MODE;
            const newMode = currentMode === 'dark' ? 'light' : 'dark';
            window.setAppThemeMode(newMode);
        };

        // Apply sidebar collapsed preference if saved
        if (localStorage.getItem('crm_sidebar_collapsed') === '1') {
            const sidebar = document.getElementById('appSidebar');
            if (sidebar) sidebar.classList.add('collapsed');
            document.documentElement.classList.add('sidebar-is-collapsed');
            document.body.classList.add('sidebar-is-collapsed');
        }

        // Run immediately
        applyThemePreferences();
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyThemePreferences);
        }
    })();
</script>
