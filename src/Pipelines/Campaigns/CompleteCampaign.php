<?php

namespace Cornatul\Marketing\Base\Pipelines\Campaigns;

use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\CampaignStatus;

class CompleteCampaign
{
    /**
     * Mark the campaign as complete in the database
     *
     * @param Campaign $schedule
     * @return Campaign
     */
    public function handle(Campaign $schedule, $next)
    {
        $this->markCampaignAsComplete($schedule);

        return $next($schedule);
    }

    /**
     * Execute the database query
     *
     * @param Campaign $campaign
     * @return void
     */
    protected function markCampaignAsComplete(Campaign $campaign): void
    {
        $campaign->status_id = CampaignStatus::STATUS_SENT;
        $campaign->save();
    }
}
