<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Validation\ValidationException;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\CampaignStatus;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignCancellationController extends Controller
{
    /** @var CampaignTenantRepositoryInterface $campaignRepository */
    private CampaignTenantRepositoryInterface $campaignRepository;

    public function __construct(CampaignTenantRepositoryInterface $campaignRepository)
    {
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @throws Exception
     */
    public function confirm(int $campaignId)
    {
        $campaign = $this->campaignRepository->find(MarketingPortal::currentWorkspaceId(), $campaignId, ['status']);

        return view('marketing::campaigns.cancel', [
            'campaign' => $campaign,
        ]);
    }

    /**
     * @throws Exception
     */
    public function cancel(int $campaignId)
    {
        /** @var Campaign $campaign */
        $campaign = $this->campaignRepository->find(MarketingPortal::currentWorkspaceId(), $campaignId, ['status']);
        $originalStatus = $campaign->status;

        if (!$campaign->canBeCancelled()) {
            throw ValidationException::withMessages([
                'campaignStatus' => "{$campaign->status->name} campaigns cannot be cancelled.",
            ])->redirectTo(route('marketing.campaigns.index'));
        }

        if ($campaign->save_as_draft && !$campaign->allDraftsCreated()) {
            throw ValidationException::withMessages([
                'messagesPendingDraft' => __('Campaigns that save draft messages cannot be cancelled until all drafts have been created.'),
            ])->redirectTo(route('marketing.campaigns.index'));
        }

        $this->campaignRepository->cancelCampaign($campaign);

        return redirect()->route('marketing.campaigns.index')->with([
            'success' => $this->getSuccessMessage($originalStatus, $campaign),
        ]);
    }

    private function getSuccessMessage(CampaignStatus $campaignStatus, Campaign $campaign): string
    {
        if ($campaignStatus->id === CampaignStatus::STATUS_QUEUED) {
            return __('The queued campaign was cancelled successfully.');
        }

        if ($campaign->save_as_draft) {
            return __('The campaign was cancelled and any remaining draft messages were deleted.');
        }

        $messageCounts = $this->campaignRepository->getCounts(collect($campaign->id), $campaign->workspace_id)[$campaign->id];

        return __(
            "The campaign was cancelled whilst being processed (~:sent/:total dispatched).",
            [
                'sent' => $messageCounts->sent,
                'total' => $campaign->active_subscriber_count
            ]
        );
    }
}
