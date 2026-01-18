<?php

namespace App\Actions\SystemSetting\User;

use App\Data\User\CreateUserData;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUserAction
{
    use AsAction;

    public function handle(CreateUserData $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'avatar' => $data->avatar,
            'password' => $data->password,
            'is_super_admin' => false,
        ]);
    }

    public function asController(Request $request)
    {
        $data = CreateUserData::from($request);
        $this->handle($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('common.操作成功'),
        ]);

        return redirect()->route('admin.get-user-list');
    }
}
