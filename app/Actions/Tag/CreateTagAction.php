<?php

namespace App\Actions\Tag;

use App\Data\Tag\FormCreateTagData;
use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateTagAction
{
    use AsAction;

    public function handle(Workspace $workspace, FormCreateTagData $data): Tag
    {
        $exists = Tag::query()
            ->where('workspace_id', $workspace->id)
            ->where('name', $data->name)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => __('common.标签名已存在'),
            ]);
        }

        return Tag::query()->create([
            'workspace_id' => $workspace->id,
            'name' => $data->name,
            'color' => $data->color,
            'description' => $data->description,
        ]);
    }

    public function asController(Request $request, Workspace $currentWorkspace)
    {
        $data = FormCreateTagData::from($request);
        $this->handle($currentWorkspace, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('workspace-setting.datas.tag', ['slug' => $currentWorkspace->slug]);
    }
}
