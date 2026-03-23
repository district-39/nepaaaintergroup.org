<?php

declare (strict_types=1);
namespace GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Serializer\EnvelopItems;

use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Event;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Profiling\Profile;
use GoDaddy\WordPress\MWC\Common\Vendor\Sentry\Util\JSON;
/**
 * @internal
 */
class ProfileItem implements EnvelopeItemInterface
{
    public static function toEnvelopeItem(Event $event): string
    {
        $header = ['type' => 'profile', 'content_type' => 'application/json'];
        $profile = $event->getSdkMetadata('profile');
        if (!$profile instanceof Profile) {
            return '';
        }
        $payload = $profile->getFormattedData($event);
        if ($payload === null) {
            return '';
        }
        return sprintf("%s\n%s", JSON::encode($header), JSON::encode($payload));
    }
}
