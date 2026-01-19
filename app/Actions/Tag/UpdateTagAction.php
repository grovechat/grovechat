<?php

namespace App\Actions\Tag;

use App\Data\Tag\FormUpdateTagData;
use App\Http\RequestContexts\WorkspaceRequestContext;
use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateTagAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $id, FormUpdateTagData $data): Tag
    {
        $tag = Tag::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($id);

        $exists = Tag::query()
            ->where('workspace_id', $workspace->id)
            ->where('name', $data->name)
            ->where('id', '!=', $tag->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => __('common.标签名已存在'),
            ]);
        }

        $tag->update([
            'name' => $data->name,
            'color' => $data->color,
            'description' => $data->description,
        ]);

        return $tag;
    }

    public function asController(Request $request, string $slug, string $id)
    {
        $currentWorkspace = WorkspaceRequestContext::fromRequest($request)->workspace;
        $data = FormUpdateTagData::from($request);
        $this->handle($currentWorkspace, $id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('workspace-setting.datas.tag', ['slug' => $currentWorkspace->slug]);
    }
}
