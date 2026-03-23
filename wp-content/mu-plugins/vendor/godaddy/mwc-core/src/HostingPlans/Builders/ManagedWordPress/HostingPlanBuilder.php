<?php

namespace GoDaddy\WordPress\MWC\Core\HostingPlans\Builders\ManagedWordPress;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\TypeHelper;
use GoDaddy\WordPress\MWC\Common\HostingPlans\Builders\Contracts\HostingPlanBuilderContract;
use GoDaddy\WordPress\MWC\Common\HostingPlans\Enums\HostingPlanNamesEnum;
use GoDaddy\WordPress\MWC\Common\Models\Contracts\HostingPlanContract;
use GoDaddy\WordPress\MWC\Common\Models\HostingPlan;
use GoDaddy\WordPress\MWC\Common\Platforms\Exceptions\PlatformRepositoryException;
use GoDaddy\WordPress\MWC\Common\Platforms\PlatformRepositoryFactory;
use GoDaddy\WordPress\MWC\Common\Traits\CanGetNewInstanceTrait;

class HostingPlanBuilder implements HostingPlanBuilderContract
{
    use CanGetNewInstanceTrait;

    /**
     * {@inheritDoc}
     */
    public function build() : HostingPlanContract
    {
        $accountPlanName = (string) StringHelper::ensureScalar(Configuration::get('godaddy.account.plan.name'));

        return HostingPlan::seed([
            'name'    => $this->getPlanId($accountPlanName),
            'label'   => $accountPlanName,
            'isTrial' => false,
        ]);
    }

    /**
     * Gets the ID of the hosting plan used by this site.
     *
     * @param string $accountPlanName
     * @return string
     */
    protected function getPlanId(string $accountPlanName) : string
    {
        if (empty($accountPlanName)) {
            return '';
        }

        try {
            $platformRepository = PlatformRepositoryFactory::getNewInstance()->getPlatformRepository();
        } catch (PlatformRepositoryException $exception) {
            return '';
        }

        if (! $platformRepository->hasPlatformData()) {
            return '';
        }

        if ($id = $this->getPlanIdFromMwpPlans($accountPlanName)) {
            return $id;
        }

        if ($id = $this->getPlanIdFromAllKnownPlans($accountPlanName)) {
            return $id;
        }

        // covers unregistered, suffixed ultimate plans, like ultimate_c19
        if ($this->isUltimatePlanType($accountPlanName)) {
            return HostingPlanNamesEnum::Ultimate;
        }

        // assume that the account is using the smaller hosting plan if we can't determine one
        return HostingPlanNamesEnum::Basic;
    }

    /**
     * Finds the MWP hosting plan ID that corresponds to the provided plan name. For example: this matches
     * plan name "eCommerce Managed WordPress" to plan ID "ecommerce".
     *
     * This checks the list of Managed WordPress product plan names only.
     *
     * @param string $accountPlanName
     * @return string|null
     */
    protected function getPlanIdFromMwpPlans(string $accountPlanName) : ?string
    {
        foreach (ArrayHelper::wrap(Configuration::get('mwp.hosting.plans')) as $id => $plan) {
            if (strtolower($accountPlanName) === strtolower(TypeHelper::string(ArrayHelper::get($plan, 'name'), ''))) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Matches the provided account plan name (lowercased) against our list of all known MWP and MWCS plans.
     *
     * {@see static::getPlanIdFromMwpPlans()} only accounts for MWP product plans.
     * This method accounts for any MWCS plans that are being hosted on the MWP platform.
     *
     * @param string $accountPlanName
     * @return string|null
     */
    protected function getPlanIdFromAllKnownPlans(string $accountPlanName) : ?string
    {
        $accountPlanName = strtolower($accountPlanName);

        foreach (ArrayHelper::wrap(Configuration::get('hosting_plans.mappings')) as $planMapping) {
            $planId = TypeHelper::string(ArrayHelper::get($planMapping, 'name'), '');

            if ($accountPlanName === strtolower($planId)) {
                return $planId;
            }
        }

        return null;
    }

    /**
     * Checks if the given plan is of a specific hosting plan type.
     *
     * @link https://github.com/gdcorp-partners/mwc-common/pull/1353/
     *
     * @param string $planName
     * @param string $targetName
     * @return bool
     */
    protected function isPlanOfType(string $planName, string $targetName) : bool
    {
        return StringHelper::startsWith(strtolower($planName), strtolower($targetName));
    }

    /**
     * Checks if the given plan is of an Ultimate hosting plan type.
     *
     * @link https://github.com/gdcorp-partners/mwc-common/pull/1353/
     *
     * @param string $planName
     * @return bool
     */
    protected function isUltimatePlanType(string $planName) : bool
    {
        return $this->isPlanOfType($planName, HostingPlanNamesEnum::Ultimate);
    }
}
