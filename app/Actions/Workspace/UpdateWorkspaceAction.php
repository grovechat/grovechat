<?php

namespace App\Actions\Workspace;

use App\Data\Workspace\FormUpdateSystemWorkspaceData;
use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateWorkspaceAction
{
    use AsAction;

    public function handle(string $id, FormUpdateSystemWorkspaceData $data)
    {
        DB::transaction(function () use ($id, $data) {
            $workspace = Workspace::query()->findOrFail($id);

            $owner = User::query()
                ->where('is_super_admin', false)
                ->findOrFail($data->owner_id);

            $oldOwnerId = filled($workspace->owner_id) ? (string) $workspace->owner_id : null;

            $workspace->update([
                'name' => $data->name,
                'slug' => $data->slug,
                'logo_id' => $data->logo_id,
                'owner_id' => $owner->id,
            ]);

            $newOwnerId = (string) $owner->id;

            if ($workspace->users()->whereKey($newOwnerId)->exists()) {
                $workspace->users()->updateExistingPivot($newOwnerId, ['role' => WorkspaceRole::OWNER->value]);
            } else {
                $workspace->users()->attach($newOwnerId, ['role' => WorkspaceRole::OWNER->value]);
            }

            if ($oldOwnerId && $oldOwnerId !== $newOwnerId) {
                if ($workspace->users()->whereKey($oldOwnerId)->exists()) {
                    $workspace->users()->updateExistingPivot($oldOwnerId, ['role' => WorkspaceRole::ADMIN->value]);
                }
            }
        });
    }

    public function asController(Request $request, string $id): RedirectResponse
    {
        $data = FormUpdateSystemWorkspaceData::from($request);
        $this->handle($id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('admin.get-workspace-list');
    }
}
