<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\FormUpdateTeammateData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTeammateAction
{
    use AsAction;

    public function handle(Workspace $workspace, $userId, FormUpdateTeammateData $data): void
    {
        $targetUser = $workspace->users()->whereKey($userId)->firstOrFail();

        if ($data->role->value !== (string) ($targetUser->pivot?->role ?? '')) {
            Gate::authorize('workspace-users.updateRole', [$workspace, $targetUser, $data->role]);
        }

        $targetUser->pivot->update([
            'role' => $data->role->value,
        ]);
    }

    public function asController(Request $request, Workspace $workspace, string $slug, string $id)
    {
        $data = FormUpdateTeammateData::from($request);
        $this->handle($workspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('show-teammate-list', ['slug' => $workspace->slug]);
    }
}
