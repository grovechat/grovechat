<?php

namespace App\Actions\User;

use App\Data\Teammate\FormUpdateTeammateOnlineStatusData;
use App\Enums\UserOnlineStatus;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateMyOnlineStatusAction
{
    use AsAction;

    public function handle(Workspace $workspace, string $userId, FormUpdateTeammateOnlineStatusData $data): void
    {
        $user = $workspace->users()->whereKey($userId)->firstOrFail();

        $pivot = ['online_status' => $data->online_status];
        if ($data->online_status == UserOnlineStatus::OFFLINE) {
            $pivot['last_active_at'] = now();
        }
        $user->pivot->update($pivot);
    }
    public function asController(Request $request, Workspace $currentWorkspace, string $slug)
    {
        $data = FormUpdateTeammateOnlineStatusData::from($request);
        $this->handle($currentWorkspace, $request->user()->id, $data);

        return back();
    }
}
