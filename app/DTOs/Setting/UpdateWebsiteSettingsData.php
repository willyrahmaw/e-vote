<?php

namespace App\DTOs\Setting;

final readonly class UpdateWebsiteSettingsData
{
    public function __construct(
        public string $appName,
        public string $institutionName,
        public ?string $appLogo = null,
        public ?string $appFavicon = null,
        public ?string $appTagline = null,
        public ?string $appDescription = null,
        public ?string $appTimezone = 'Asia/Jakarta',
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
        public ?string $institutionAddress = null,
        public ?string $footerCopyright = null,
        public bool $enablePublicMonitor = true,
        public bool $enableBallotVerification = true,
        public ?string $announcementBanner = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            appName: trim($data['app_name'] ?? 'E-Voting Terpadu'),
            institutionName: trim($data['institution_name'] ?? ''),
            appLogo: isset($data['app_logo']) ? trim($data['app_logo']) : null,
            appFavicon: isset($data['app_favicon']) ? trim($data['app_favicon']) : null,
            appTagline: isset($data['app_tagline']) ? trim($data['app_tagline']) : null,
            appDescription: isset($data['app_description']) ? trim($data['app_description']) : null,
            appTimezone: in_array($data['app_timezone'] ?? '', ['Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura']) ? $data['app_timezone'] : 'Asia/Jakarta',
            contactEmail: isset($data['contact_email']) ? trim($data['contact_email']) : null,
            contactPhone: isset($data['contact_phone']) ? trim($data['contact_phone']) : null,
            institutionAddress: isset($data['institution_address']) ? trim($data['institution_address']) : null,
            footerCopyright: isset($data['footer_copyright']) ? trim($data['footer_copyright']) : null,
            enablePublicMonitor: (bool) ($data['enable_public_monitor'] ?? true),
            enableBallotVerification: (bool) ($data['enable_ballot_verification'] ?? true),
            announcementBanner: isset($data['announcement_banner']) ? trim($data['announcement_banner']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'app_name' => $this->appName,
            'institution_name' => $this->institutionName,
            'app_logo' => $this->appLogo ?? '',
            'app_favicon' => $this->appFavicon ?? '',
            'app_tagline' => $this->appTagline ?? '',
            'app_description' => $this->appDescription ?? '',
            'app_timezone' => $this->appTimezone ?? 'Asia/Jakarta',
            'contact_email' => $this->contactEmail ?? '',
            'contact_phone' => $this->contactPhone ?? '',
            'institution_address' => $this->institutionAddress ?? '',
            'footer_copyright' => $this->footerCopyright ?? '',
            'enable_public_monitor' => $this->enablePublicMonitor ? '1' : '0',
            'enable_ballot_verification' => $this->enableBallotVerification ? '1' : '0',
            'announcement_banner' => $this->announcementBanner ?? '',
        ];
    }
}
