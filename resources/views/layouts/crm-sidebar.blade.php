<!-- Immediate Theme Preloader (Prevents Theme Flash) -->
<script>
    (function(){
        document.documentElement.setAttribute('data-theme-color', localStorage.getItem('crm_theme_color') || 'red');
        document.documentElement.setAttribute('data-theme-mode', localStorage.getItem('crm_theme_mode') || 'light');
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
        <!-- PROMINENT GREEN ACTIVE PILL: DASHBOARD (MATCHING SCREENSHOT) -->
        @php
            $dashboardRoute = match($roleId) {
                4 => route('leads.allotted'),
                default => route('crm.alias', ['tab' => 'overview']),
            };
            $currentTab = $activeTab ?? request()->query('tab', '');
            $isDashboardActive = ($currentTab === 'overview' || empty($currentTab)) 
                && !request()->routeIs('leads.*') 
                && !request()->routeIs('finance.*') 
                && !request()->routeIs('sms.*')
                && !request()->routeIs('kpi.*')
                && !request()->routeIs('crm.std*');
            if ($roleId == 4) {
                $isDashboardActive = request()->routeIs('leads.allotted') && empty($currentTab);
            }
        @endphp

        <a href="{{ $dashboardRoute }}" class="menu-btn-active-dashboard {{ $isDashboardActive ? 'active' : '' }}" id="nav-btn-dashboard" onclick="selectDashboardNav(event)" title="Portal Overview">
            <svg viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span>Dashboard</span>
        </a>

        @if($roleId == 3 || $roleId == 2)
            <!-- ================= ROLE 3 (CRM DEAN) & ROLE 2 (CRM INCHARGE) ================= -->
            
            <!-- SECTION 1: CRM / MANAGE (Collapsible Dropdown with Chevron) -->
            @php $isCrmOpen = in_array($activeTab ?? '', ['stddata', 'stdsearch']) || request()->routeIs('crm.std*'); @endphp
            <div class="accordion-group {{ $isCrmOpen ? 'open' : '' }}" id="group-crm">
                <button type="button" class="accordion-header {{ $isCrmOpen ? 'active' : '' }}" onclick="toggleAccordion('group-crm')">
                    <div class="header-left">
                        <!-- Manage / 4-Squares Grid SVG (Matching Screenshot) -->
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <span>CRM</span>
                    </div>
                    <!-- Chevron Down SVG (Matching Screenshot) -->
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('crm.stddata', ['tab' => 'stddata']) }}" class="submenu-link {{ ($activeTab ?? '') === 'stddata' ? 'active' : '' }}">
                        Student data
                    </a>
                    <a href="{{ route('crm.stdsearch', ['tab' => 'stdsearch']) }}" class="submenu-link {{ ($activeTab ?? '') === 'stdsearch' ? 'active' : '' }}">
                        Student search
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 2: LEADS (Collapsible Dropdown with Chevron) -->
            @php $isLeadsOpen = request()->routeIs('leads.*') || in_array($activeTab ?? '', ['allocation', 'allotted', 'callbacks']); @endphp
            <div class="accordion-group {{ $isLeadsOpen ? 'open' : '' }}" id="group-leads">
                <button type="button" class="accordion-header {{ $isLeadsOpen ? 'active' : '' }}" onclick="toggleAccordion('group-leads')">
                    <div class="header-left">
                        <!-- Target / Leads SVG -->
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                        <span>Leads</span>
                    </div>
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('leads.allocation', ['tab' => 'allocation']) }}" class="submenu-link {{ ($activeTab ?? '') === 'allocation' ? 'active' : '' }}">
                        Leads Allocation
                    </a>
                    <a href="{{ route('leads.index') }}" class="submenu-link {{ request()->routeIs('leads.index') && ($activeTab ?? '') !== 'allocation' ? 'active' : '' }}">
                        Leads Directory
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 3: FINANCE (Collapsible Dropdown with Chevron) -->
            @php $isFinOpen = request()->routeIs('finance.*') || in_array($activeTab ?? '', ['paiddetails']); @endphp
            <div class="accordion-group {{ $isFinOpen ? 'open' : '' }}" id="group-finance">
                <button type="button" class="accordion-header {{ $isFinOpen ? 'active' : '' }}" onclick="toggleAccordion('group-finance')">
                    <div class="header-left">
                        <!-- Building / Civil Services Bank SVG (Matching Screenshot) -->
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <line x1="3" y1="21" x2="21" y2="21"/>
                            <line x1="6" y1="18" x2="6" y2="11"/>
                            <line x1="10" y1="18" x2="10" y2="11"/>
                            <line x1="14" y1="18" x2="14" y2="11"/>
                            <line x1="18" y1="18" x2="18" y2="11"/>
                            <polygon points="12 2 20 7 4 7"/>
                        </svg>
                        <span>Finance</span>
                    </div>
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('finance.paiddetails', ['tab' => 'paiddetails']) }}" class="submenu-link {{ ($activeTab ?? '') === 'paiddetails' ? 'active' : '' }}">
                        Paid details
                    </a>
                    <a href="{{ route('finance.index') }}" class="submenu-link {{ request()->routeIs('finance.index') && ($activeTab ?? '') !== 'paiddetails' ? 'active' : '' }}">
                        Fee Overview
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 4: KPI (Collapsible Dropdown with Chevron) -->
            @php $isKpiOpen = in_array($activeTab ?? '', ['kpi', 'studentkpi']) || request()->routeIs('kpi.*'); @endphp
            <div class="accordion-group {{ $isKpiOpen ? 'open' : '' }}" id="group-kpi">
                <button type="button" class="accordion-header {{ $isKpiOpen ? 'active' : '' }}" onclick="toggleAccordion('group-kpi')">
                    <div class="header-left">
                        <!-- Graduation / Academics SVG (Matching Screenshot) -->
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        <span>KPI</span>
                    </div>
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('kpi.studentkpi', ['tab' => 'studentkpi']) }}" class="submenu-link {{ ($activeTab ?? '') === 'studentkpi' ? 'active' : '' }}">
                        student KPI
                    </a>
                    <a href="{{ route('kpi.index', ['tab' => 'kpi']) }}" class="submenu-link {{ ($activeTab ?? '') === 'kpi' ? 'active' : '' }}">
                        KPI Overview
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 5: COMMUNICATIONS -->
            <div class="accordion-group" id="group-comm">
                <a href="{{ route('sms.index') }}" class="accordion-header {{ request()->routeIs('sms.*') ? 'active' : '' }}" style="text-decoration: none;">
                    <div class="header-left">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span>Communications</span>
                    </div>
                </a>
            </div>

        @elseif($roleId == 4)
            <!-- ================= ROLE 4 (TELE CALLER) ================= -->
            <!-- SECTION 1: LEADS (Collapsible Dropdown with Chevron) -->
            <div class="accordion-group open" id="group-telecaller-leads">
                <button type="button" class="accordion-header active" onclick="toggleAccordion('group-telecaller-leads')">
                    <div class="header-left">
                        <!-- Manage / 4-Squares Grid SVG (Matching Screenshot) -->
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                        <span>Leads</span>
                    </div>
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('leads.allotted', ['tab' => 'allotted']) }}" class="submenu-link {{ ($activeTab ?? 'allotted') === 'allotted' ? 'active' : '' }}">
                        Allotted Leads
                    </a>
                    <a href="{{ route('leads.index', ['tab' => 'all']) }}" class="submenu-link {{ ($activeTab ?? '') === 'all' ? 'active' : '' }}">
                        Calling Queue
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 2: FOLLOW-UPS -->
            <div class="accordion-group" id="group-telecaller-followups">
                <a href="{{ route('leads.index', ['tab' => 'callbacks', 'status' => 'callback']) }}" class="accordion-header {{ ($activeTab ?? '') === 'callbacks' ? 'active' : '' }}" style="text-decoration: none;">
                    <div class="header-left">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Scheduled Callbacks</span>
                    </div>
                </a>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 3: COMMUNICATIONS -->
            <div class="accordion-group" id="group-telecaller-comm">
                <a href="{{ route('sms.index') }}" class="accordion-header {{ request()->routeIs('sms.*') ? 'active' : '' }}" style="text-decoration: none;">
                    <div class="header-left">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span>SMS / WhatsApp</span>
                    </div>
                </a>
            </div>

        @else
            <!-- ================= ROLE 1 (EMPLOYEE / ADMIN) ================= -->
            <!-- SECTION 1: STUDENT REGISTRY -->
            @php $isEmpCrmOpen = in_array($activeTab ?? '', ['stddata', 'stdsearch']) || request()->routeIs('crm.std*'); @endphp
            <div class="accordion-group {{ $isEmpCrmOpen ? 'open' : '' }}" id="group-emp-crm">
                <button type="button" class="accordion-header {{ $isEmpCrmOpen ? 'active' : '' }}" onclick="toggleAccordion('group-emp-crm')">
                    <div class="header-left">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="3" width="7" height="7" rx="1"/>
                            <rect x="14" y="14" width="7" height="7" rx="1"/>
                            <rect x="3" y="14" width="7" height="7" rx="1"/>
                        </svg>
                        <span>Manage</span>
                    </div>
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('crm.stddata', ['tab' => 'stddata']) }}" class="submenu-link {{ ($activeTab ?? '') === 'stddata' ? 'active' : '' }}">
                        Student data
                    </a>
                    <a href="{{ route('crm.stdsearch', ['tab' => 'stdsearch']) }}" class="submenu-link {{ ($activeTab ?? '') === 'stdsearch' ? 'active' : '' }}">
                        Student search
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 2: ACADEMICS (Degree Programs & Branches) -->
            @php $isEmpAcadOpen = in_array($activeTab ?? '', ['programs', 'branches', 'campuses']); @endphp
            <div class="accordion-group {{ $isEmpAcadOpen ? 'open' : '' }}" id="group-emp-academics">
                <button type="button" class="accordion-header {{ $isEmpAcadOpen ? 'active' : '' }}" onclick="toggleAccordion('group-emp-academics')">
                    <div class="header-left">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        <span>Academics</span>
                    </div>
                    <svg class="chevron-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>
                <div class="accordion-body">
                    <a href="{{ route('crm.alias', ['tab' => 'programs']) }}" class="submenu-link {{ ($activeTab ?? '') === 'programs' ? 'active' : '' }}">
                        Degree Programs
                    </a>
                    <a href="{{ route('crm.alias', ['tab' => 'branches']) }}" class="submenu-link {{ ($activeTab ?? '') === 'branches' ? 'active' : '' }}">
                        Academic Branches
                    </a>
                    <a href="{{ route('crm.alias', ['tab' => 'campuses']) }}" class="submenu-link {{ ($activeTab ?? '') === 'campuses' ? 'active' : '' }}">
                        Campuses
                    </a>
                </div>
            </div>

            <div class="sidebar-divider"></div>

            <!-- SECTION 3: COMMUNICATIONS -->
            <div class="accordion-group" id="group-emp-comm">
                <a href="{{ route('sms.index') }}" class="accordion-header {{ request()->routeIs('sms.*') ? 'active' : '' }}" style="text-decoration: none;">
                    <div class="header-left">
                        <svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <span>Communications</span>
                    </div>
                </a>
            </div>
        @endif
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
        }

        // Run immediately
        applyThemePreferences();
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyThemePreferences);
        }
    })();
</script>
