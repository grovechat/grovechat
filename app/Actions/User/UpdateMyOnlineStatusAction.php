<?php

namespace App\Actions\User;

use App\Data\Teammate\FormUpdateTeammateOnlineStatusData;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMyOnlineStatusAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $userId, FormUpdateTeammateOnlineStatusData $data): void
    {
        $user = $workspace->users()->whereKey($userId)->firstOrFail();

        $user->pivot->update([
            'online_status' => $data->online_status->value,
        ]);
    }

    public function asController(Request $request, Workspace $currentWorkspace, string $slug)
    {
        $data = FormUpdateTeammateOnlineStatusData::from($request);
        $this->handle($currentWorkspace, $request->user()->id, $data);

        return back();
    }
}
