<?php

namespace App\Common\Services;

use Modules\Auth\Models\Menu;
use Modules\Auth\Models\MenuAccessRole;
use Modules\Auth\Models\Role;
use Illuminate\Support\Facades\Log;

class MenuService
{
    /**
     * Get structured navigation menus for the authenticated user and their active role.
     */
    public static function getMenusForUser($user): array
    {
        if (!$user) {
            return [];
        }

        $roleId = method_exists($user, 'getActiveRoleId') ? $user->getActiveRoleId() : ($user->role_id ?? 2);
        $roleMenus = MenuAccessRole::forRole($roleId);

        $menuGroups = [];
        $currentMainId = null;
        $kpiMainId = null;

        foreach ($roleMenus as $access) {
            $menu = $access->menu;
            if (!$menu) continue;

            $isSmsMenu = ($menu->menu_id == 117 || str_contains(strtolower($menu->menu_name), 'sms') || $menu->controller_ref === 'sms/index');
            $isKpiMainMenu = ($menu->menu_id == 103 || $menu->menu_code === 'KPII0001' || strtolower(trim($menu->menu_name)) === 'kpi' || $menu->controller_ref === 'kpi/index');

            if ((int)$access->mposition === 1) {
                $menuGroups[$menu->menu_id] = [
                    'main' => $menu,
                    'access' => $access,
                    'submenus' => [],
                ];
                if (!$isSmsMenu) {
                    $currentMainId = $menu->menu_id;
                }
                if ($isKpiMainMenu) {
                    $kpiMainId = $menu->menu_id;
                }
            } elseif ((int)$access->mposition === 2) {
                $isKpiSub = str_contains(strtolower($menu->controller_ref ?? ''), 'kpi') 
                    || str_starts_with($menu->menu_code ?? '', 'KPII0002') 
                    || str_starts_with($menu->menu_code ?? '', 'KPII0005') 
                    || str_starts_with($menu->menu_code ?? '', 'KPII0006');
                
                if ($isKpiSub && $kpiMainId && isset($menuGroups[$kpiMainId])) {
                    $parentId = $kpiMainId;
                } else {
                    $parentId = $menu->parent_menu_id ?: $currentMainId;
                }

                if ($parentId == 117 || (isset($menuGroups[$parentId]) && str_contains(strtolower($menuGroups[$parentId]['main']->menu_name), 'sms'))) {
                    $parentId = $kpiMainId ?: $currentMainId;
                }

                if ($parentId && isset($menuGroups[$parentId])) {
                    $menuGroups[$parentId]['submenus'][] = [
                        'menu' => $menu,
                        'access' => $access,
                    ];
                } else {
                    $ref = strtolower($menu->controller_ref ?? '');
                    $code = strtoupper($menu->menu_code ?? '');
                    $targetMainId = null;

                    if (str_contains($ref, 'kpi') || str_starts_with($code, 'KPI')) {
                        $targetMainId = $kpiMainId ?: 103;
                    } elseif (str_contains($ref, 'leads') || str_starts_with($code, 'LEAD')) {
                        $targetMainId = 101;
                    } elseif (str_contains($ref, 'crm') || str_starts_with($code, 'CRM') || preg_match('/step\d+/i', $ref)) {
                        $targetMainId = 100;
                    } elseif (str_contains($ref, 'finance') || str_starts_with($code, 'FINA')) {
                        $targetMainId = 102;
                    } elseif (str_contains($ref, 'exam') || str_starts_with($code, 'EXAM')) {
                        $targetMainId = 125;
                    }

                    if ($targetMainId && isset($menuGroups[$targetMainId])) {
                        $menuGroups[$targetMainId]['submenus'][] = [
                            'menu' => $menu,
                            'access' => $access,
                        ];
                    }
                }
            }
        }

        // SMS Tracker is standalone (no submenus)
        foreach ($menuGroups as $mId => &$grp) {
            if ($grp['main']->menu_id == 117 || str_contains(strtolower($grp['main']->menu_name), 'sms') || $grp['main']->controller_ref === 'sms/index') {
                $grp['submenus'] = [];
            }
        }
        unset($grp);

        // KPI strictly holds: Student KPI, Telecaller KPI, Leads KPI
        foreach ($menuGroups as $mId => &$grp) {
            $isActualKpiMenu = (
                $grp['main']->menu_id == 103 
                || $grp['main']->menu_code === 'KPII0001' 
                || strtolower(trim($grp['main']->menu_name)) === 'kpi' 
                || $grp['main']->controller_ref === 'kpi/index'
            ) && !str_contains(strtolower($grp['main']->menu_name), 'sms');

            if ($isActualKpiMenu) {
                $filteredSubmenus = [];
                foreach ($grp['submenus'] as $subItem) {
                    $ref = strtolower(trim($subItem['menu']->controller_ref ?? ''));
                    $name = strtolower(trim($subItem['menu']->menu_name ?? ''));
                    if (!str_contains($ref, 'counseller') && !str_contains($name, 'counseller') && !str_contains($ref, 'sms')) {
                        $filteredSubmenus[] = $subItem;
                    }
                }
                $grp['submenus'] = $filteredSubmenus;

                $targetKpis = [
                    ['code' => 'KPII0002', 'name' => 'Student KPI', 'ref' => 'kpi/studentkpi', 'id' => 108],
                    ['code' => 'KPII0005', 'name' => 'Telecaller KPI', 'ref' => 'kpi/telecallerkpi', 'id' => 128],
                    ['code' => 'KPII0006', 'name' => 'Leads KPI', 'ref' => 'kpi/leadskpi', 'id' => 129],
                ];

                foreach ($targetKpis as $kpiTarget) {
                    $found = false;
                    foreach ($grp['submenus'] as &$subItem) {
                        $ref = strtolower(trim($subItem['menu']->controller_ref ?? ''));
                        if ($ref === strtolower($kpiTarget['ref']) || str_contains($ref, strtolower(str_replace(' ', '', $kpiTarget['name'])))) {
                            $subItem['menu']->menu_name = $kpiTarget['name'];
                            $subItem['menu']->controller_ref = $kpiTarget['ref'];
                            $found = true;
                            break;
                        }
                    }
                    unset($subItem);

                    if (!$found) {
                        $fakeMenu = (object)[
                            'menu_id' => $kpiTarget['id'],
                            'menu_code' => $kpiTarget['code'],
                            'menu_name' => $kpiTarget['name'],
                            'controller_ref' => $kpiTarget['ref'],
                            'icon_name' => 'fa fa-chart-line',
                            'parent_menu_id' => $grp['main']->menu_id,
                        ];
                        $grp['submenus'][] = [
                            'menu' => $fakeMenu,
                            'access' => null,
                        ];
                    }
                }
            }
        }
        unset($grp);

        return $menuGroups;
    }

    /**
     * Resolve the route URL for a given controller reference.
     * Uses defensive Route::has() checks to guarantee zero RouteNotFoundExceptions.
     */
    public static function resolveMenuUrl(?string $controllerRef): string
    {
        $controllerRef = trim((string)$controllerRef);

        if (preg_match('/step\D*(\d+)/i', $controllerRef, $matches)) {
            $step = (int)$matches[1];
            return \Illuminate\Support\Facades\Route::has('dashboards.index')
                ? route('dashboards.index', ['step' => $step])
                : url('/step' . $step);
        }
        if (str_starts_with($controllerRef, 'dashboards?step=')) {
            $step = (int)str_replace('dashboards?step=', '', $controllerRef);
            return \Illuminate\Support\Facades\Route::has('dashboards.index')
                ? route('dashboards.index', ['step' => $step])
                : url('/step' . $step);
        }

        $safeRoute = function(string $routeName, array $params = []) use ($controllerRef): string {
            return \Illuminate\Support\Facades\Route::has($routeName)
                ? route($routeName, $params)
                : url('/' . ltrim($controllerRef, '/'));
        };

        return match ($controllerRef) {
            'crm/stddata' => $safeRoute('crm.stddata', ['tab' => 'stddata']),
            'crm/stdsearch' => $safeRoute('crm.stdsearch', ['tab' => 'stdsearch']),
            'crm/crmdatatracking', 'crm/datatracking' => $safeRoute('crm.crmdatatracking', ['tab' => 'stdsearch']),
            'coupoungenration', 'crm/coupoungenration', 'crm/couponinfo', 'crm/coupon-info' => $safeRoute('crm.couponinfo', ['tab' => 'couponinfo']),
            'crm/index' => $safeRoute('crm.stddata', ['tab' => 'stddata']),
            'leads/allottedleads' => $safeRoute('leads.allotted'),
            'leads/leadsallocation' => $safeRoute('leads.allocation', ['tab' => 'allocation']),
            'leads/leadsupload' => $safeRoute('leads.upload', ['tab' => 'upload']),
            'leads/leads input', 'leads/leadsinput', 'leads/lead-input', 'leads/leads-input' => $safeRoute('leads.input', ['tab' => 'input']),
            'leads/index' => $safeRoute('leads.index'),
            'finance/paiddetails' => $safeRoute('finance.paiddetails', ['tab' => 'paiddetails']),
            'finance/payfee' => $safeRoute('dashboards.index', ['step' => 1]),
            'finance/index' => $safeRoute('finance.index'),
            'exam/examslotdata', 'exam/index', 'examslotdata' => $safeRoute('exam.examslotdata'),
            'kpi/studentkpi' => $safeRoute('kpi.studentkpi', ['tab' => 'studentkpi']),
            'kpi/telecallerkpi' => $safeRoute('kpi.telecallerkpi', ['tab' => 'telecallerkpi']),
            'kpi/leadskpi' => $safeRoute('kpi.leadskpi', ['tab' => 'leadskpi']),
            'kpi/counsellerkpi' => $safeRoute('kpi.counsellerkpi', ['tab' => 'counsellerkpi']),
            'kpi/index' => $safeRoute('kpi.index', ['tab' => 'kpi']),
            'sms/index', 'sms' => $safeRoute('sms.index'),
            default => url('/' . ltrim($controllerRef, '/')),
        };
    }

    /**
     * Check if a submenu item is active based on current path and active tab.
     */
    public static function isSubActive(?string $controllerRef, string $currentPath, ?string $currentTab = null): bool
    {
        $controllerRef = trim((string)$controllerRef);
        $currentPath = trim($currentPath, '/');
        $currentTab = (string)$currentTab;

        if (preg_match('/step\D*(\d+)/i', $controllerRef, $matches)) {
            $stepNum = (int)$matches[1];
            return (request()->routeIs('dashboards.*') && request()->query('step', 1) == $stepNum) || $currentPath === 'step' . $stepNum;
        }

        return match ($controllerRef) {
            'crm/stddata' => $currentTab === 'stddata' || request()->routeIs('crm.stddata') || $currentPath === 'crm/stddata',
            'crm/crmdatatracking', 'crm/datatracking' => request()->routeIs('crm.crmdatatracking') || str_contains($currentPath, 'crmdatatracking'),
            'crm/stdsearch' => (request()->routeIs('crm.stdsearch') && !str_contains($currentPath, 'crmdatatracking')) || ($currentPath === 'crm/stdsearch' && !str_contains($currentPath, 'crmdatatracking')),
            'coupoungenration', 'crm/coupoungenration', 'crm/couponinfo', 'crm/coupon-info' => $currentTab === 'couponinfo' || request()->routeIs('crm.couponinfo*') || request()->routeIs('crm.coupoungenration*') || str_contains($currentPath, 'coupoungenration') || str_contains($currentPath, 'coupon-info') || str_contains($currentPath, 'couponinfo'),
            'leads/allottedleads' => request()->routeIs('leads.allotted') || str_contains($currentPath, 'allotted'),
            'leads/leadsallocation' => $currentTab === 'allocation' || request()->routeIs('leads.allocation') || str_contains($currentPath, 'allocation'),
            'leads/leadsupload' => $currentTab === 'upload' || request()->routeIs('leads.upload') || str_contains($currentPath, 'leadsupload'),
            'leads/leads input', 'leads/leadsinput', 'leads/lead-input', 'leads/leads-input' => $currentTab === 'input' || request()->routeIs('leads.input') || str_contains($currentPath, 'leadsinput') || str_contains($currentPath, 'leads-input') || str_contains($currentPath, 'leads%20input') || str_contains($currentPath, 'leads input'),
            'finance/paiddetails' => request()->routeIs('finance.paiddetails') || str_contains($currentPath, 'paiddetails'),
            'finance/payfee' => str_contains($currentPath, 'payfee'),
            'exam/examslotdata', 'exam/index' => request()->routeIs('exam.*') || str_contains($currentPath, 'exam'),
            'kpi/studentkpi' => request()->routeIs('kpi.studentkpi') || $currentTab === 'studentkpi' || str_contains($currentPath, 'studentkpi'),
            'kpi/telecallerkpi' => request()->routeIs('kpi.telecallerkpi') || $currentTab === 'telecallerkpi' || str_contains($currentPath, 'telecallerkpi'),
            'kpi/leadskpi' => request()->routeIs('kpi.leadskpi') || $currentTab === 'leadskpi' || str_contains($currentPath, 'leadskpi'),
            'kpi/counsellerkpi' => request()->routeIs('kpi.counsellerkpi') || $currentTab === 'counsellerkpi' || str_contains($currentPath, 'counsellerkpi'),
            default => $currentPath === trim($controllerRef, '/'),
        };
    }

    /**
     * Get SVG icon markup for a menu item.
     */
    public static function getMenuSvgIcon(?string $iconName, ?string $menuCode): string
    {
        $code = strtoupper((string)$menuCode);
        $icon = (string)$iconName;

        if (str_starts_with($code, 'DASH')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>';
        }
        if (str_starts_with($code, 'CRM')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>';
        }
        if (str_starts_with($code, 'LEADS')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="6"/><circle cx="12" cy="2"/></svg>';
        }
        if (str_starts_with($code, 'FINA')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="3" y1="21" x2="21" y2="21"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg>';
        }
        if (str_starts_with($code, 'EXAM')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
        }
        if (str_contains($icon, 'envelope') || str_starts_with($code, 'SMS')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
        }
        if (str_starts_with($code, 'KPI')) {
            return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
        }
        return '<svg class="menu-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="9"/></svg>';
    }

    /**
     * Check if dashboard is active for the current user and view.
     */
    public static function isDashboardActive(?int $roleId, ?string $currentTab = null): bool
    {
        if (RoleService::isTelecaller((int)$roleId)) {
            return request()->routeIs('leads.allotted') && empty($currentTab);
        }

        return ($currentTab === 'overview' || empty($currentTab))
            && !request()->routeIs('leads.*')
            && !request()->routeIs('finance.*')
            && !request()->routeIs('sms.*')
            && !request()->routeIs('kpi.*')
            && !request()->routeIs('crm.std*');
    }
}

