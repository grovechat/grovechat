<?php

namespace App\Actions\User;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreUserAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id): void
    {
        $user = $workspace->users()
            ->onlyTrashed()
            ->whereKey($id)
            ->firstOrFail();

        $user->restore();
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug, string $id)
    {
        $this->handle($currentWorkspace, $id);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
