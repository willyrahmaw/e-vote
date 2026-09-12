<?php

namespace App\Actions\Setting;

use App\DTOs\Setting\UpdateWebsiteSettingsData;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use App\Services\Setting\SettingService;
use Illuminate\Support\Facades\DB;

class UpdateWebsiteSettingsAction
{
    public function __construct(
        private SettingService $settingService,
        private AuditLogService $auditService,
    ) {}

    public function execute(User $admin, UpdateWebsiteSettingsData $data): void
    {
        DB::transaction(function () use ($admin, $data) {
            $this->settingService->updateMany($data->toArray());

            $this->auditService->log(
                action: 'UPDATE_WEBSITE_SETTINGS',
                user: $admin,
                metadata: [
                    'admin_email' => $admin->email,
                    'app_name' => $data->appName,
                    'institution_name' => $data->institutionName,
                ]
            );
        });
    }
}
