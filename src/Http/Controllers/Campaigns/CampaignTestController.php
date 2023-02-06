<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\CampaignTestRequest;
use Cornatul\Marketing\Base\Services\Messages\DispatchTestMessage;

class CampaignTestController extends Controller
{
    /** @var DispatchTestMessage */
    protected DispatchTestMessage $dispatchTestMessage;

    public function __construct(DispatchTestMessage $dispatchTestMessage)
    {
        $this->dispatchTestMessage = $dispatchTestMessage;
    }

    /**
     * @throws Exception
     */
    public function handle(CampaignTestRequest $request, int $campaignId): RedirectResponse
    {
        $messageId = $this->dispatchTestMessage->handle(MarketingPortal::currentWorkspaceId(), $campaignId, $request->get('recipient_email'));

        if (!$messageId) {
            return redirect()->route('marketing.campaigns.preview', $campaignId)
                ->withInput()
                ->with(['error', __('Failed to dispatch test email.')]);
        }

        return redirect()->route('marketing.campaigns.preview', $campaignId)
            ->withInput()
            ->with(['success' => __('The test email has been dispatched.')]);
    }
}
