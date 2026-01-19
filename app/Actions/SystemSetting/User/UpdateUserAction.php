<?php

namespace App\Actions\SystemSetting\User;

use App\Data\User\FormUpdateUserData;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserAction
{
    use AsAction;

    public function handle(string $id, FormUpdateUserData $data): void
    {
        $user = User::query()
            ->where('is_super_admin', false)
            ->findOrFail($id);

        $user->update([
            'name' => $data->name,
            'email' => $data->email,
            'avatar' => $data->avatar,
        ]);

        if (filled($data->password)) {
            $user->update([
                'password' => $data->password,
            ]);
        }
    }

    public function asController(Request $request, string $id)
    {
        $data = FormUpdateUserData::from($request);
        $this->handle($id, $data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('admin.get-user-list');
    }
}
