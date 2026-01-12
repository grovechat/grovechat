<?php

namespace App\Actions\StorageSetting\StorageProfile;

use App\Actions\StorageSetting\CheckStorageSettingAction;
use App\Data\StorageSettingCheckData;
use App\Models\StorageProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;
use Throwable;

class CreateStorageProfileAction
{
    use AsAction;

    public function __construct(
        protected CheckStorageSettingAction $checker,
    ) {}

    public function asController(Request $request, string $slug)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:64'],
            'provider' => ['required', 'string'],
            'region' => ['required', 'string'],
            'endpoint' => ['required', 'string', 'url'],
            'bucket' => ['required', 'string'],
            'key' => ['required', 'string'],
            'secret' => ['required', 'string'],
            'url' => ['nullable', 'string', 'url'],
        ]);

        $checkData = StorageSettingCheckData::from([
            'provider' => $validated['provider'],
            'key' => $validated['key'],
            'secret' => $validated['secret'],
            'bucket' => $validated['bucket'],
            'region' => $validated['region'],
            'endpoint' => $validated['endpoint'],
            'url' => $validated['url'] ?? null,
        ]);

        try {
            $this->checker->handle($checkData);
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'secret' => '连接检测失败，请检查配置与网络连通性',
            ]);
        }

        StorageProfile::query()->create([
            'name' => $validated['name'],
            'provider' => $validated['provider'],
            'region' => $validated['region'],
            'endpoint' => $validated['endpoint'],
            'bucket' => $validated['bucket'],
            'key' => $validated['key'],
            'secret' => $validated['secret'],
            'url' => $validated['url'] ?? null,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '已创建存储配置',
        ]);

        return back();
    }
}

