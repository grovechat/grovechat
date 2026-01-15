<?php

namespace App\Actions\Workspace;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreWorkspaceAction
{
    use AsAction;

    public function handle(string $id): void
    {
        $workspace = Workspace::onlyTrashed()->findOrFail($id);
        $workspace->restore();
    }

    public function asController(Request $request, string $id)
    {
        $this->handle($id);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return back();
    }
}
