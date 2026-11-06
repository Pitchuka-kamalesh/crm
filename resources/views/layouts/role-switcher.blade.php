@php
    $rolesList = $availableRoles ?? \App\Models\Role::orderBy('role_id')->get();
    $activeRoleId = $roleId ?? (auth()->user() ? auth()->user()->getActiveRoleId() : 1);
@endphp

@if(isset($rolesList) && $rolesList->isNotEmpty())
    <form action="{{ route('role.switch') }}" method="POST" class="top-role-switch-form">
        @csrf
        <div class="top-role-switch-pill">
            <span class="top-role-icon">🔄</span>
            <span class="top-role-label">Switch Role:</span>
            <select name="role_id" class="top-role-select" onchange="this.form.submit()" title="Switch active portal role">
                @foreach($rolesList as $r)
                    <option value="{{ $r->role_id }}" {{ (int)$activeRoleId === (int)$r->role_id ? 'selected' : '' }}>
                        {{ $r->role_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
@endif
