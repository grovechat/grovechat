<?php

namespace App\Actions\Workspace;

use App\Data\Workspace\FormCreateSystemWorkspaceData;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateWorkspaceAction
{
    use AsAction;

    public function handle(FormCreateSystemWorkspaceData $data): Workspace
    {
        return DB::transaction(function () use ($data) {
            $owner = User::query()
                ->where('is_super_admin', false)
                ->findOrFail($data->owner_id);

            $workspace = Workspace::query()->create([
                'name' => $data->name,
                'slug' => $data->slug,
                'logo_id' => $data->logo_id,
                'owner_id' => $owner->id,
            ]);

            $workspace->users()->attach($owner->id, [
                'role' => WorkspaceRole::OWNER->value,
            ]);

            return $workspace;
        });
    }

    public function asController(Request $request): RedirectResponse
    {
        $data = FormCreateSystemWorkspaceData::from($request);
        $this->handle($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('admin.get-workspace-list');
    }
}
