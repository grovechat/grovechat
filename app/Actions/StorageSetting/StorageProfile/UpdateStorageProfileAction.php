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

class UpdateStorageProfileAction
{
    use AsAction;

    public function __construct(
        protected CheckStorageSettingAction $checker,
    ) {}

    public function asController(Request $request, string $slug, StorageProfile $profile)
    {
        // 设计约束：Profile 一旦创建，bucket/endpoint/region/provider 固定不变（避免历史附件指向漂移）
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:64'],
            'url' => ['nullable', 'string', 'url'],
            // 合并“更新凭证”：不填表示不修改
            'key' => ['nullable', 'string'],
            'secret' => ['nullable', 'string'],
        ]);

        $update = [
            'name' => $validated['name'],
            'url' => $validated['url'] ?? null,
        ];

        // 只有同时提交 key+secret 才更新凭证（避免只改一半）
        $keyProvided = filled($validated['key'] ?? null);
        $secretProvided = filled($validated['secret'] ?? null);
        if ($keyProvided || $secretProvided) {
            if (! $keyProvided || ! $secretProvided) {
                throw ValidationException::withMessages([
                    'secret' => '更新凭证需要同时填写 Key 和 Secret',
                ]);
            }

            $checkData = StorageSettingCheckData::from([
                'provider' => $profile->provider,
                'key' => $validated['key'],
                'secret' => $validated['secret'],
                'bucket' => $profile->bucket,
                'region' => $profile->region,
                'endpoint' => $profile->endpoint,
                'url' => $validated['url'] ?? $profile->url,
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

            $update['key'] = $validated['key'];
            $update['secret'] = $validated['secret'];
        }

        $profile->update([
            ...$update,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => '已更新存储配置',
        ]);

        return back();
    }
}

