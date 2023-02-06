<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Presenters\CampaignReportPresenter;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\Messages\MessageTenantRepositoryInterface;

class CampaignReportsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected CampaignTenantRepositoryInterface $campaignRepo;

    /** @var MessageTenantRepositoryInterface */
    protected MessageTenantRepositoryInterface $messageRepo;

    public function __construct(
        CampaignTenantRepositoryInterface $campaignRepository,
        MessageTenantRepositoryInterface $messageRepo
    ) {
        $this->campaignRepo = $campaignRepository;
        $this->messageRepo = $messageRepo;
    }

    /**
     * Show campaign report view.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function index(int $id, Request $request)
    {
        $campaign = $this->campaignRepo->find(MarketingPortal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('marketing.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $presenter = new CampaignReportPresenter($campaign, MarketingPortal::currentWorkspaceId(), (int) $request->get('interval', 24));
        $presenterData = $presenter->generate();

        $data = [
            'campaign' => $campaign,
            'campaignUrls' => $presenterData['campaignUrls'],
            'campaignStats' => $presenterData['campaignStats'],
            'chartLabels' => json_encode(Arr::get($presenterData['chartData'], 'labels', [])),
            'chartData' => json_encode(Arr::get($presenterData['chartData'], 'data', [])),
        ];

        return view('marketing::campaigns.reports.index', $data);
    }

    /**
     * Show campaign recipients.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function recipients(int $id)
    {
        $campaign = $this->campaignRepo->find(MarketingPortal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('marketing.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $messages = $this->messageRepo->recipients(MarketingPortal::currentWorkspaceId(), Campaign::class, $id);

        return view('marketing::campaigns.reports.recipients', compact('campaign', 'messages'));
    }

    /**
     * Show campaign opens.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function opens(int $id)
    {
        $campaign = $this->campaignRepo->find(MarketingPortal::currentWorkspaceId(), $id);
        $averageTimeToOpen = $this->campaignRepo->getAverageTimeToOpen($campaign);

        if ($campaign->draft) {
            return redirect()->route('marketing.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $messages = $this->messageRepo->opens(MarketingPortal::currentWorkspaceId(), Campaign::class, $id);

        return view('marketing::campaigns.reports.opens', compact('campaign', 'messages', 'averageTimeToOpen'));
    }

    /**
     * Show campaign clicks.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function clicks(int $id)
    {
        $campaign = $this->campaignRepo->find(MarketingPortal::currentWorkspaceId(), $id);
        $averageTimeToClick = $this->campaignRepo->getAverageTimeToClick($campaign);

        if ($campaign->draft) {
            return redirect()->route('marketing.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $messages = $this->messageRepo->clicks(MarketingPortal::currentWorkspaceId(), Campaign::class, $id);

        return view('marketing::campaigns.reports.clicks', compact('campaign', 'messages', 'averageTimeToClick'));
    }

    /**
     * Show campaign bounces.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function bounces(int $id)
    {
        $campaign = $this->campaignRepo->find(MarketingPortal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('marketing.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $messages = $this->messageRepo->bounces(MarketingPortal::currentWorkspaceId(), Campaign::class, $id);

        return view('marketing::campaigns.reports.bounces', compact('campaign', 'messages'));
    }

    /**
     * Show campaign unsubscribes.
     *
     * @return RedirectResponse|View
     * @throws Exception
     */
    public function unsubscribes(int $id)
    {
        $campaign = $this->campaignRepo->find(MarketingPortal::currentWorkspaceId(), $id);

        if ($campaign->draft) {
            return redirect()->route('marketing.campaigns.edit', $id);
        }

        if ($campaign->queued || $campaign->sending) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $messages = $this->messageRepo->unsubscribes(MarketingPortal::currentWorkspaceId(), Campaign::class, $id);

        return view('marketing::campaigns.reports.unsubscribes', compact('campaign', 'messages'));
    }
}
