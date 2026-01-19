<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteTagAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id): void
    {
        $tag = Tag::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($id);

        $tag->delete();
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug, string $id)
    {
        $this->handle($currentWorkspace, $id);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('workspace-setting.datas.tag', ['slug' => $currentWorkspace->slug]);
    }
}
