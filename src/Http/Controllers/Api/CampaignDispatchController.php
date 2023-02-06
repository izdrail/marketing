<?php

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\CampaignDispatchRequest;
use Cornatul\Marketing\Base\Http\Resources\Campaign as CampaignResource;
use Cornatul\Marketing\Base\Interfaces\QuotaServiceInterface;
use Cornatul\Marketing\Base\Models\CampaignStatus;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignDispatchController extends Controller
{
    /**
     * @var CampaignTenantRepositoryInterface
     */
    protected $campaigns;

    /**
     * @var QuotaServiceInterface
     */
    protected $quotaService;

    public function __construct(
        CampaignTenantRepositoryInterface $campaigns,
        QuotaServiceInterface $quotaService
    ) {
        $this->campaigns = $campaigns;
        $this->quotaService = $quotaService;
    }

    /**
     * @throws \Exception
     */
    public function send(CampaignDispatchRequest $request, $campaignId)
    {
        $campaign = $request->getCampaign(['email_service', 'messages']);
        $workspaceId = MarketingPortal::currentWorkspaceId();

        if ($this->quotaService->exceedsQuota($campaign->email_service, $campaign->unsent_count)) {
            return response([
                'message' => __('The number of subscribers for this campaign exceeds your SES quota')
            ], 422);
        }

        $campaign = $this->campaigns->update($workspaceId, $campaignId, [
            'status_id' => CampaignStatus::STATUS_QUEUED,
        ]);

        return new CampaignResource($campaign);
    }
}
