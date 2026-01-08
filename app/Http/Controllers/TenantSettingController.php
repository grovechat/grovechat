<?php

namespace App\Http\Controllers;

use App\Data\UpdateTenantDTO;
use App\Models\Tenant;
use Inertia\Inertia;

class TenantSettingController extends Controller
{
    public function __construct(
        public Tenant $tenant
    ) { }    

    public function showTenantGeneralPage()
    {
        return Inertia::render("tenantSettings/tenant/General");
    }
    
    public function updateTenantGeneral(UpdateTenantDTO $dto)
    {
        $this->tenant->update($dto->toArray()); 
        
        return back();
    }
}
