<?php

namespace App\Actions\Teammate;

use App\Data\Teammate\FormUpdateTeammateData;
use App\Http\RequestContexts\WorkspaceRequestContext;
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

        $currentNickname = filled($targetUser->pivot?->nickname) ? (string) $targetUser->pivot->nickname : null;
        $nextNickname = filled($data->nickname) ? $data->nickname : null;

        if ($nextNickname !== $currentNickname) {
            Gate::authorize('workspace-users.updateProfile', [$workspace, $targetUser]);
        }

        if ($data->role->value !== (string) ($targetUser->pivot?->role ?? '')) {
            Gate::authorize('workspace-users.updateRole', [$workspace, $targetUser, $data->role]);
        }

        $targetUser->pivot->update([
            'nickname' => $nextNickname,
            'role' => $data->role->value,
        ]);
    }

    public function asController(Request $request, string $slug, string $id)
    {
        $workspace = WorkspaceRequestContext::fromRequest($request)->workspace;
        $data = FormUpdateTeammateData::from($request);
        $this->handle($workspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('show-teammate-list', ['slug' => $workspace->slug]);
    }
}
