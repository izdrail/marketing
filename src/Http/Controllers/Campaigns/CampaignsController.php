<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Campaigns;

use Exception;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\CampaignStoreRequest;
use Cornatul\Marketing\Base\Models\EmailService;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\EmailServiceTenantRepository;
use Cornatul\Marketing\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Cornatul\Marketing\Base\Repositories\TagTenantRepository;
use Cornatul\Marketing\Base\Repositories\TemplateTenantRepository;
use Cornatul\Marketing\Base\Services\Campaigns\CampaignStatisticsService;

class CampaignsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    protected CampaignTenantRepositoryInterface $campaigns;

    /** @var TemplateTenantRepository */
    protected TemplateTenantRepository $templates;

    /** @var TagTenantRepository */
    protected TagTenantRepository $tags;

    /** @var EmailServiceTenantRepository */
    protected EmailServiceTenantRepository $emailServices;

    /** @var SubscriberTenantRepositoryInterface */
    protected SubscriberTenantRepositoryInterface $subscribers;

    /**
     * @var CampaignStatisticsService
     */
    protected CampaignStatisticsService $campaignStatisticsService;

    public function __construct(
        CampaignTenantRepositoryInterface $campaigns,
        TemplateTenantRepository $templates,
        TagTenantRepository $tags,
        EmailServiceTenantRepository $emailServices,
        SubscriberTenantRepositoryInterface $subscribers,
        CampaignStatisticsService $campaignStatisticsService
    ) {
        $this->campaigns = $campaigns;
        $this->templates = $templates;
        $this->tags = $tags;
        $this->emailServices = $emailServices;
        $this->subscribers = $subscribers;
        $this->campaignStatisticsService = $campaignStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): ViewContract
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $params = ['draft' => true];
        $campaigns = $this->campaigns->paginate($workspaceId, 'created_atDesc', ['status'], 25, $params);

        return view('marketing::campaigns.index', [
            'campaigns' => $campaigns,
            'campaignStats' => $this->campaignStatisticsService->getForPaginator($campaigns, $workspaceId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function sent(): ViewContract
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $params = ['sent' => true];
        $campaigns = $this->campaigns->paginate($workspaceId, 'created_atDesc', ['status'], 25, $params);

        return view('marketing::campaigns.index', [
            'campaigns' => $campaigns,
            'campaignStats' => $this->campaignStatisticsService->getForPaginator($campaigns, $workspaceId),
        ]);
    }

    /**
     * @throws Exception
     */
    public function create(): ViewContract
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $templates = [null => '- None -'] + $this->templates->pluck($workspaceId);
        $emailServices = $this->emailServices->all(MarketingPortal::currentWorkspaceId(), 'id', ['type'])
            ->map(static function (EmailService $emailService) {
                $emailService->formatted_name = "{$emailService->name} ({$emailService->type->name})";
                return $emailService;
            });

        return view('marketing::campaigns.create', compact('templates', 'emailServices'));
    }

    /**
     * @throws Exception
     */
    public function store(CampaignStoreRequest $request): RedirectResponse
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $campaign = $this->campaigns->store($workspaceId, $this->handleCheckboxes($request->validated()));

        return redirect()->route('marketing.campaigns.preview', $campaign->id);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): ViewContract
    {
        $campaign = $this->campaigns->find(MarketingPortal::currentWorkspaceId(), $id);

        return view('marketing::campaigns.show', compact('campaign'));
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): ViewContract
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $campaign = $this->campaigns->find($workspaceId, $id);
        $emailServices = $this->emailServices->all($workspaceId, 'id', ['type'])
            ->map(static function (EmailService $emailService) {
                $emailService->formatted_name = "{$emailService->name} ({$emailService->type->name})";
                return $emailService;
            });
        $templates = [null => '- None -'] + $this->templates->pluck($workspaceId);

        return view('marketing::campaigns.edit', compact('campaign', 'emailServices', 'templates'));
    }

    /**
     * @throws Exception
     */
    public function update(int $campaignId, CampaignStoreRequest $request): RedirectResponse
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $campaign = $this->campaigns->update(
            $workspaceId,
            $campaignId,
            $this->handleCheckboxes($request->validated())
        );

        return redirect()->route('marketing.campaigns.preview', $campaign->id);
    }

    /**
     * @return RedirectResponse|ViewContract
     * @throws Exception
     */
    public function preview(int $id)
    {
        $campaign = $this->campaigns->find(MarketingPortal::currentWorkspaceId(), $id);
        $subscriberCount = $this->subscribers->countActive(MarketingPortal::currentWorkspaceId());

        if (!$campaign->draft) {
            return redirect()->route('marketing.campaigns.status', $id);
        }

        $tags = $this->tags->all(MarketingPortal::currentWorkspaceId(), 'name');

        return view('marketing::campaigns.preview', compact('campaign', 'tags', 'subscriberCount'));
    }

    /**
     * @return RedirectResponse|ViewContract
     * @throws Exception
     */
    public function status(int $id)
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $campaign = $this->campaigns->find($workspaceId, $id, ['status']);

        if ($campaign->sent) {
            return redirect()->route('marketing.campaigns.reports.index', $id);
        }

        return view('marketing::campaigns.status', [
            'campaign' => $campaign,
            'campaignStats' => $this->campaignStatisticsService->getForCampaign($campaign, $workspaceId),
        ]);
    }

    /**
     * Handle checkbox fields.
     *
     * NOTE(david): this is here because the Campaign model is marked as being unable to use boolean fields.
     */
    private function handleCheckboxes(array $input): array
    {
        $checkboxFields = [
            'is_open_tracking',
            'is_click_tracking'
        ];

        foreach ($checkboxFields as $checkboxField) {
            if (!isset($input[$checkboxField])) {
                $input[$checkboxField] = false;
            }
        }

        return $input;
    }
}
