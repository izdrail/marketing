<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\CampaignStatus;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Cornatul\Marketing\Base\Services\Campaigns\CampaignDispatchService;

class CampaignDispatchCommand extends Command
{
    /** @var string */
    protected  $signature = 'sp:campaigns:dispatch';

    /** @var string */
    protected   $description = 'Dispatch all campaigns waiting in the queue';

    /** @var CampaignTenantRepositoryInterface */
    protected CampaignTenantRepositoryInterface $campaignRepo;

    /** @var CampaignDispatchService */
    protected CampaignDispatchService $campaignService;

    public function handle(
        CampaignTenantRepositoryInterface $campaignRepo,
        CampaignDispatchService $campaignService
    ): void {
        $this->campaignRepo = $campaignRepo;
        $this->campaignService = $campaignService;

        $campaigns = $this->getQueuedCampaigns();
        $count = count($campaigns);

        if (! $count) {
            return;
        }

        $this->info('Dispatching campaigns count=' . $count);

        foreach ($campaigns as $campaign) {
            $message = 'Dispatching campaign id=' . $campaign->id;

            $this->info($message);
            info($message);
            $count++;

            $this->campaignService->handle($campaign);
        }

        $message = 'Finished dispatching campaigns';
        $this->info($message);
        info($message);
    }

    /**
     * Get all queued campaigns.
     */
    protected function getQueuedCampaigns(): EloquentCollection
    {
        return Campaign::where('status_id', CampaignStatus::STATUS_QUEUED)
            ->where('scheduled_at', '<=', now())
            ->get();
    }
}
