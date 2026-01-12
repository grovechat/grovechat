<?php

namespace App\Actions\SystemSetting;

use App\Data\GeneralSettingsData;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateGeneralSettingAction
{
    use AsAction;

    public function __construct(
        public GeneralSettings $settings,
    ) {}

    public function handle(GeneralSettingsData $data)
    {
        $this->settings->lock('version');
        $this->settings->fill($data->toArray())->save();
    }

    public function asController(Request $request)
    {
        $this->handle(GeneralSettingsData::from($request));

        return back();
    }
}
