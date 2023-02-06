<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\CampaignStoreRequest;
use Cornatul\Marketing\Base\Http\Resources\Campaign as CampaignResource;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignsController extends Controller
{
    /** @var CampaignTenantRepositoryInterface */
    private CampaignTenantRepositoryInterface $campaigns;

    public function __construct(CampaignTenantRepositoryInterface $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();

        return CampaignResource::collection($this->campaigns->paginate($workspaceId, 'id', ['tags']));
    }

    /**
     * @throws Exception
     */
    public function store(CampaignStoreRequest $request): CampaignResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $data = Arr::except($request->validated(), ['tags']);

        $data['save_as_draft'] = $request->get('save_as_draft') ?? 0;

        $campaign = $this->campaigns->store($workspaceId, $data);

        $campaign->tags()->sync($request->get('tags'));

        return new CampaignResource($campaign);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): CampaignResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $campaign = $this->campaigns->find($workspaceId, $id);

        return new CampaignResource($campaign);
    }

    /**
     * @throws Exception
     */
    public function update(CampaignStoreRequest $request, int $id): CampaignResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $data = Arr::except($request->validated(), ['tags']);

        $data['save_as_draft'] = $request->get('save_as_draft') ?? 0;

        $campaign = $this->campaigns->update($workspaceId, $id, $data);

        $campaign->tags()->sync($request->get('tags'));

        return new CampaignResource($campaign);
    }
}
