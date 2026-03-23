<?php
namespace RSSSL\Security\WordPress\Two_Fa\Services;

class Rsssl_Two_Fa_Status_Service {

    /**
     * Determine the two-factor status for a user.
     *
     * @return string
     */
    public function determineStatus(int $userId, array $forcedRoles, int $daysThreshold): string {
        $totpStatus   = get_user_meta($userId, 'rsssl_two_fa_status_totp', true);
        $emailStatus  = get_user_meta($userId, 'rsssl_two_fa_status_email', true);
        $passkeyStatus  = get_user_meta($userId, 'rsssl_two_fa_status_passkey', true);
        $lastLogin    = get_user_meta($userId, 'rsssl_two_fa_last_login', true);

        // User has active 2FA configured
        if (in_array('active', [$totpStatus, $emailStatus, $passkeyStatus], true)) {
            return 'active';
        }

        // User has explicitly disabled 2FA
        if ($totpStatus === 'disabled' && $emailStatus === 'disabled') {
            return 'disabled';
        }

        // Check if user has a forced role
        $userData = get_userdata($userId);
        $userRoles = $userData ? $userData->roles : [];
        $isForced = !empty(array_intersect($forcedRoles, $userRoles));

        // Non-forced user: return based on method status or default to open
        if (!$isForced) {
            return $totpStatus ?: $emailStatus ?: 'open';
        }

        // New user without lastLogin - initialize grace period
        if (empty($lastLogin)) {
            update_user_meta($userId, 'rsssl_two_fa_last_login', gmdate('Y-m-d H:i:s'));
            return 'open';
        }

        // Grace period has expired
        $lastLoginTime = strtotime($lastLogin);
        $thresholdTime = strtotime("-$daysThreshold days");

        if ($lastLoginTime !== false && $lastLoginTime < $thresholdTime) {
            return 'expired';
        }

        // Still within grace period
        return $totpStatus ?: $emailStatus ?: 'open';
    }
}