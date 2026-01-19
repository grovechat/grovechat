<?php

namespace App\Actions\Tag;

use App\Data\Tag\ListTagItemData;
use App\Data\Tag\ShowListTagPagePropsData;
use App\Data\WorkspaceUserContextData;
use App\Models\Tag;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class ShowTagListAction
{
    use AsAction;

    public function handle(Workspace $workspace): ShowListTagPagePropsData
    {
        $tags = $workspace->tags()
            ->orderBy('name', 'asc')
            ->get();

        return new ShowListTagPagePropsData(
            tag_list: $tags->map(fn (Tag $tag) => ListTagItemData::fromModel($tag))->all(),
        );
    }

    public function asController(Request $request)
    {
        $currentWorkspace = WorkspaceUserContextData::fromRequest($request)->workspace();
        $props = $this->handle($currentWorkspace);

        return Inertia::render('tags/Index', $props->toArray());
    }
}
