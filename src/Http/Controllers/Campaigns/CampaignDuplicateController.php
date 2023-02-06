<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Models\CampaignStatus;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignDuplicateController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected CampaignTenantRepositoryInterface $campaigns;

    public function __construct(CampaignTenantRepositoryInterface $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * Duplicate a campaign.
     *
     * @throws Exception
     */
    public function duplicate(int $campaignId): RedirectResponse
    {
        $campaign = $this->campaigns->find(MarketingPortal::currentWorkspaceId(), $campaignId);

        return redirect()->route('marketing.campaigns.create')->withInput([
            'name' => $campaign->name . ' - Duplicate',
            'status_id' => CampaignStatus::STATUS_DRAFT,
            'template_id' => $campaign->template_id,
            'email_service_id' => $campaign->email_service_id,
            'subject' => $campaign->subject,
            'content' => $campaign->content,
            'from_name' => $campaign->from_name,
            'from_email' => $campaign->from_email,
        ]);
    }
}
