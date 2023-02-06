<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\TagSubscriberDestroyRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\TagSubscriberStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\TagSubscriberUpdateRequest;
use Cornatul\Marketing\Base\Http\Resources\Subscriber as SubscriberResource;
use Cornatul\Marketing\Base\Repositories\TagTenantRepository;
use Cornatul\Marketing\Base\Services\Tags\ApiTagSubscriberService;

class TagSubscribersController extends Controller
{
    /** @var TagTenantRepository */
    private TagTenantRepository $tags;

    /** @var ApiTagSubscriberService */
    private ApiTagSubscriberService $apiService;

    public function __construct(
        TagTenantRepository $tags,
        ApiTagSubscriberService $apiService
    ) {
        $this->tags = $tags;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(int $tagId): AnonymousResourceCollection
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $tag = $this->tags->find($workspaceId, $tagId, ['subscribers']);

        return SubscriberResource::collection($tag->subscribers);
    }

    /**
     * @throws Exception
     */
    public function store(TagSubscriberStoreRequest $request, int $tagId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscribers = $this->apiService->store($workspaceId, $tagId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function update(TagSubscriberUpdateRequest $request, int $tagId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscribers = $this->apiService->update($workspaceId, $tagId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function destroy(TagSubscriberDestroyRequest $request, int $tagId): AnonymousResourceCollection
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscribers = $this->apiService->destroy($workspaceId, $tagId, collect($input['subscribers']));

        return SubscriberResource::collection($subscribers);
    }
}
