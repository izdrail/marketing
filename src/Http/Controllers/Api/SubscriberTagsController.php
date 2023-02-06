<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\SubscriberTagDestroyRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\SubscriberTagStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\SubscriberTagUpdateRequest;
use Cornatul\Marketing\Base\Http\Resources\Tag as TagResource;
use Cornatul\Marketing\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Cornatul\Marketing\Base\Services\Subscribers\Tags\ApiSubscriberTagService;

class SubscriberTagsController extends Controller
{
    /** @var SubscriberTenantRepositoryInterface */
    private SubscriberTenantRepositoryInterface $subscribers;

    /** @var ApiSubscriberTagService */
    private ApiSubscriberTagService $apiService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        ApiSubscriberTagService $apiService
    ) {
        $this->subscribers = $subscribers;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(int $subscriberId): AnonymousResourceCollection
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscriber = $this->subscribers->find($workspaceId, $subscriberId, ['tags']);

        return TagResource::collection($subscriber->tags);
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberTagStoreRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $tags = $this->apiService->store($workspaceId, $subscriberId, collect($input['tags']));

        return TagResource::collection($tags);
    }

    /**
     * @throws Exception
     */
    public function update(SubscriberTagUpdateRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $tags = $this->apiService->update($workspaceId, $subscriberId, collect($input['tags']));

        return TagResource::collection($tags);
    }

    /**
     * @throws Exception
     */
    public function destroy(SubscriberTagDestroyRequest $request, int $subscriberId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $tags = $this->apiService->destroy($workspaceId, $subscriberId, collect($input['tags']));

        return TagResource::collection($tags);
    }
}
