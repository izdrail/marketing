<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignDeleteController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected CampaignTenantRepositoryInterface $campaigns;

    public function __construct(CampaignTenantRepositoryInterface $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * Show a confirmation view prior to deletion.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function confirm(int $id)
    {
        $campaign = $this->campaigns->find(MarketingPortal::currentWorkspaceId(), $id);

        if (!$campaign->draft) {
            return redirect()->route('marketing.campaigns.index')
                ->withErrors(__('Unable to delete a campaign that is not in draft status'));
        }

        return view('marketing::campaigns.delete', compact('campaign'));
    }

    /**
     * Delete a campaign from the database.
     *
     * @throws Exception
     */
    public function destroy(Request $request): RedirectResponse
    {
        $campaign = $this->campaigns->find(MarketingPortal::currentWorkspaceId(), $request->get('id'));

        if (!$campaign->draft) {
            return redirect()->route('marketing.campaigns.index')
                ->withErrors(__('Unable to delete a campaign that is not in draft status'));
        }

        $this->campaigns->destroy(MarketingPortal::currentWorkspaceId(), $request->get('id'));

        return redirect()->route('marketing.campaigns.index')
            ->with('success', __('The Campaign has been successfully deleted'));
    }
}
