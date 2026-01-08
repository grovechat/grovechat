<?php

namespace App\Http\Controllers;

use App\Data\UpdateTenantDTO;
use App\Exceptions\BusinessException;
use App\Models\Tenant;
use Inertia\Inertia;

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
    
    public function updateTenent(UpdateTenantDTO $dto)
    {
        $this->tenant->update($dto->toArray());
        
        return redirect(route('tenant-setting.tenant.general', $this->tenant->path));
    }
    
    public function deleteTenant()
    {
        if (!empty($this->tenant->owner_id)) {
            throw new BusinessException("不能删除默认工作区");
        }
        
        $this->tenant->delete();
    }
}
