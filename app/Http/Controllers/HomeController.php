<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Features;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    }
    
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();

        $lastTenantPath = session('last_tenant_path');
        if ($lastTenantPath) {
            $tenant = $user->tenants()->where('path', $lastTenantPath)->first();
            if ($tenant) {
                return redirect()->route('tenant.dashboard', ['tenant_path' => $tenant->path]);
            }
        }

        // 如果没有保存的工作区或无权访问，跳转到第一个工作区
        $firstTenant = $user->tenants()->first();
        if ($firstTenant) {
            return redirect()->route('tenant.dashboard', ['tenant_path' => $firstTenant->path]);
        }
        return redirect()->route('home');
    }
    
    public function tenantHome(Tenant $tenant)
    {
        return redirect()->route('tenant.dashboard', $tenant->path);
    }
    
    public function tenantDashboard()
    {
        return Inertia::render('Dashboard');
    }
}
