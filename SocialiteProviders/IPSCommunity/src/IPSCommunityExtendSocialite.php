<?php

namespace SocialiteProviders\IPSCommunity;

use SocialiteProviders\Manager\SocialiteWasCalled;

class IPSCommunityExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('ipscommunity', \SocialiteProviders\IPSCommunity\Provider::class);
    }
}
