<?php

namespace App\Http\Controllers;

use App\Data\CreateTenantDTO;
use App\Data\UpdateTenantDTO;
use App\Enums\TenantRole;
use App\Exceptions\BusinessException;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TenantSettingController extends Controller
{
    public function __construct(
        public Tenant $tenant
    ) { }    

    public function showTenantGeneralPage()
    {
        return Inertia::render("tenantSettings/tenant/General", [
            'tenant' => $this->tenant,
        ]);
    }
    
    public function showCreateTenantPage()
    {
        return Inertia::render('tenantSettings/tenant/CreateTenant');
    }
    
    public function updateTenent(UpdateTenantDTO $dto)
    {
        $this->tenant->update($dto->toArray());
        
        return redirect(route('tenant-setting.tenant.general', $this->tenant->path));
    }
    
    public function storeTenant(CreateTenantDTO $dto)
    {
        $newTenant = DB::transaction(function () use ($dto) {
            $tenant = Tenant::query()->create($dto->toArray());
            $tenant->createSlug();

            /** @var User $user */
            $user = Auth::user();
            $user->tenants()->attach($tenant->id, ['role' => TenantRole::ADMIN]);

            return $tenant;
        });

        return redirect(route('tenant-setting.tenant.general', $newTenant->path));
    }
    
    public function deleteTenant()
    {
        if (!empty($this->tenant->owner_id)) {
            throw new BusinessException("不能删除默认工作区");
        }
        
        $this->tenant->delete();
        
        $defaultTenant = Tenant::query()->where('owner_id', Auth::id())->firstOrFail();

        return redirect(route('tenant-setting.tenant.general', $defaultTenant->path));
    }
}
