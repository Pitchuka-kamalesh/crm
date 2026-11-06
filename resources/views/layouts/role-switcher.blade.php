@php
    $currentUser = auth()->user();
    $userNameRaw = $currentUser?->name ?? 'Y.BHANUPRASAD';
    $userInitial = strtoupper(substr(trim($userNameRaw), 0, 1));
    $userNameDisplay = strtoupper($userNameRaw);
    $activeRoleId = $roleId ?? ($currentUser ? $currentUser->getActiveRoleId() : 1);
    $activeRoleName = $roleName ?? ($currentUser ? $currentUser->role_label : 'Results Coordinator');

    $allRolesList = $availableRoles ?? \App\Models\Role::orderBy('role_id')->get();
    if ($allRolesList->isEmpty()) {
        $allRolesList = collect([
            (object)['role_id' => 3, 'role_name' => 'CRM DEAN'],
            (object)['role_id' => 2, 'role_name' => 'CRM Incharge'],
            (object)['role_id' => 4, 'role_name' => 'Tele Caller'],
            (object)['role_id' => 1, 'role_name' => 'Admin / Employee'],
            (object)['role_id' => 5, 'role_name' => 'Student Candidate'],
        ]);
    }

    $roleIcons = [
        3 => '🏛️',
        2 => '👔',
        4 => '🎧',
        1 => '💼',
        5 => '🎓',
    ];
@endphp

<!-- EXACT USER PROFILE & ROLE SWITCHER PILL (MATCHING USER SCREENSHOT) -->
<div class="user-role-dropdown-wrapper" id="userRoleDropdownWrapper">
    <button type="button" class="user-profile-pill-btn" id="userProfilePillBtn" onclick="toggleUserRoleMenu(event)" aria-expanded="false" title="Switch Role / Account Details">
        <!-- Red Circular Avatar with User Initial -->
        <div class="user-avatar-red">
            {{ $userInitial }}
        </div>

        <!-- User Name & Role Text Stack -->
        <div class="user-text-stack">
            <span class="user-name-bold">{{ $userNameDisplay }}</span>
            <span class="user-role-sub">{{ $activeRoleName }}</span>
        </div>

        <!-- Caret Down Icon -->
        <svg class="user-caret-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </button>

    <!-- Floating Dropdown Popover Menu -->
    <div class="user-role-dropdown-card" id="userRoleDropdownCard">
        <div class="role-dropdown-header">
            <span class="role-dropdown-label">SWITCH ACTIVE ROLE</span>
            <span class="role-dropdown-hint">Select a role to preview portal view</span>
        </div>

        <!-- Available Roles List -->
        <div class="role-dropdown-items-list">
            @foreach($allRolesList as $r)
                @php
                    $rId = (int)$r->role_id;
                    $isCurrent = ((int)$activeRoleId === $rId);
                    $icon = $roleIcons[$rId] ?? '👤';
                @endphp
                <form action="{{ route('role.switch') }}" method="POST" class="role-switch-item-form">
                    @csrf
                    <input type="hidden" name="role_id" value="{{ $rId }}">
                    <button type="submit" class="role-menu-item {{ $isCurrent ? 'is-active-role' : '' }}">
                        <span class="role-menu-icon">{{ $icon }}</span>
                        <span class="role-menu-text">{{ $r->role_name }}</span>
                        @if($isCurrent)
                            <span class="role-check-mark">✓</span>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>

        <div class="role-dropdown-divider"></div>

        <!-- Logout Action (Included as requested: "include logout init") -->
        <div class="role-dropdown-footer">
            <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
                @csrf
                <button type="submit" class="role-menu-logout-btn">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .user-role-dropdown-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .user-profile-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        padding: 3px 12px 3px 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease-in-out;
        outline: none;
        user-select: none;
    }

    .user-profile-pill-btn:hover,
    .user-profile-pill-btn.active {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .user-profile-pill-btn:focus-visible {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .user-avatar-red {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.88rem;
        line-height: 1;
        box-shadow: 0 2px 5px rgba(220, 38, 38, 0.35);
        flex-shrink: 0;
    }

    .user-text-stack {
        display: flex;
        flex-direction: column;
        text-align: left;
        line-height: 1.15;
    }

    .user-name-bold {
        font-size: 0.78rem;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.01em;
        white-space: nowrap;
    }

    .user-role-sub {
        font-size: 0.68rem;
        font-weight: 600;
        color: #64748b;
        white-space: nowrap;
        margin-top: 1px;
    }

    .user-caret-icon {
        color: #94a3b8;
        margin-left: 0.15rem;
        transition: transform 0.2s ease;
        flex-shrink: 0;
    }

    .user-profile-pill-btn.active .user-caret-icon {
        transform: rotate(180deg);
        color: #475569;
    }

    .user-role-dropdown-card {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 250px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        z-index: 1000;
        display: none;
        opacity: 0;
        transform: translateY(-6px);
        transition: opacity 0.15s ease, transform 0.15s ease;
        overflow: hidden;
    }

    .user-role-dropdown-card.is-open {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .role-dropdown-header {
        padding: 0.65rem 0.85rem;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }

    .role-dropdown-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .role-dropdown-hint {
        display: block;
        font-size: 0.68rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .role-dropdown-items-list {
        padding: 0.35rem;
        display: flex;
        flex-direction: column;
        gap: 2px;
        max-height: 240px;
        overflow-y: auto;
    }

    .role-switch-item-form {
        margin: 0;
        width: 100%;
    }

    .role-menu-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.5rem 0.65rem;
        border: none;
        background: transparent;
        border-radius: 6px;
        cursor: pointer;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 600;
        color: #334155;
        text-align: left;
        transition: background 0.12s ease, color 0.12s ease;
    }

    .role-menu-item:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .role-menu-item.is-active-role {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
    }

    .role-menu-icon {
        font-size: 0.88rem;
        line-height: 1;
        width: 20px;
        text-align: center;
    }

    .role-menu-text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .role-check-mark {
        color: #2563eb;
        font-weight: 800;
        font-size: 0.85rem;
        line-height: 1;
    }

    .role-dropdown-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 0.25rem 0;
    }

    .role-dropdown-footer {
        padding: 0.35rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }

    .role-menu-logout-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.5rem 0.65rem;
        border: none;
        background: transparent;
        border-radius: 6px;
        color: #dc2626;
        font-family: inherit;
        font-size: 0.78rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.12s ease;
        text-align: left;
    }

    .role-menu-logout-btn:hover {
        background: #fee2e2;
        color: #b91c1c;
    }
</style>

<script>
    if (typeof window.toggleUserRoleMenu === 'undefined') {
        window.toggleUserRoleMenu = function(e) {
            e.stopPropagation();
            const btn = document.getElementById('userProfilePillBtn');
            const card = document.getElementById('userRoleDropdownCard');
            if (card) {
                const isOpen = card.classList.toggle('is-open');
                if (btn) btn.classList.toggle('active', isOpen);
            }
        };

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('userRoleDropdownWrapper');
            const btn = document.getElementById('userProfilePillBtn');
            const card = document.getElementById('userRoleDropdownCard');
            if (card && card.classList.contains('is-open')) {
                if (wrapper && !wrapper.contains(e.target)) {
                    card.classList.remove('is-open');
                    if (btn) btn.classList.remove('active');
                }
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const btn = document.getElementById('userProfilePillBtn');
                const card = document.getElementById('userRoleDropdownCard');
                if (card && card.classList.contains('is-open')) {
                    card.classList.remove('is-open');
                    if (btn) btn.classList.remove('active');
                }
            }
        });
    }
</script>
