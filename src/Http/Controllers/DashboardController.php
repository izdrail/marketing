<?php

namespace Cornatul\Marketing\Base\Http\Controllers;

use Carbon\CarbonPeriod;
use Exception;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Cornatul\Marketing\Base\Services\Campaigns\CampaignStatisticsService;

class DashboardController extends Controller
{
    /**
     * @var SubscriberTenantRepositoryInterface
     */
    protected SubscriberTenantRepositoryInterface $subscribers;

    /**
     * @var CampaignTenantRepositoryInterface
     */
    protected CampaignTenantRepositoryInterface $campaigns;

    /**
     * @var MessageTenantRepositoryInterface
     */
    protected MessageTenantRepositoryInterface $messages;

    /**
     * @var CampaignStatisticsService
     */
    protected CampaignStatisticsService $campaignStatisticsService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        CampaignTenantRepositoryInterface $campaigns,
        MessageTenantRepositoryInterface $messages,
        CampaignStatisticsService $campaignStatisticsService
    )
    {
        $this->subscribers = $subscribers;
        $this->campaigns = $campaigns;
        $this->messages = $messages;
        $this->campaignStatisticsService = $campaignStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $completedCampaigns = $this->campaigns->completedCampaigns($workspaceId, ['status']);
        $subscriberGrowthChart = $this->getSubscriberGrowthChart($workspaceId);

        //todo create here a class that will transform data
        return view('marketing::dashboard.index', [
            'recentSubscribers' => $this->subscribers->getRecentSubscribers($workspaceId),
            'completedCampaigns' => $completedCampaigns,
            'campaignStats' => $this->campaignStatisticsService->getForCollection($completedCampaigns, $workspaceId),
            'subscriberGrowthChartLabels' => json_encode($subscriberGrowthChart['labels']),
            'subscriberGrowthChartData' => json_encode($subscriberGrowthChart['data']),
        ]);
    }

    protected function getSubscriberGrowthChart($workspaceId): array
    {
        $period = CarbonPeriod::create(now()->subDays(30)->startOfDay(), now()->endOfDay());

        $growthChartData = $this->subscribers->getGrowthChartData($period, $workspaceId);

        $growthChart = [
            'labels' => [],
            'data' => [],
        ];

        $currentTotal = $growthChartData['startingValue'];

        foreach ($period as $date) {
            $formattedDate = $date->format('d-m-Y');

            $periodValue = $growthChartData['runningTotal'][$formattedDate]->total ?? 0;
            $periodUnsubscribe = $growthChartData['unsubscribers'][$formattedDate]->total ?? 0;
            $currentTotal += $periodValue - $periodUnsubscribe;

            $growthChart['labels'][] = $formattedDate;
            $growthChart['data'][] = $currentTotal;
        }

        return $growthChart;
    }
}
