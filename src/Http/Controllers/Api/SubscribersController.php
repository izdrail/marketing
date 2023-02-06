<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\SubscriberStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\SubscriberUpdateRequest;
use Cornatul\Marketing\Base\Http\Resources\Subscriber as SubscriberResource;
use Cornatul\Marketing\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Cornatul\Marketing\Base\Services\Subscribers\ApiSubscriberService;

class SubscribersController extends Controller
{
    /** @var SubscriberTenantRepositoryInterface */
    protected SubscriberTenantRepositoryInterface $subscribers;

    /** @var ApiSubscriberService */
    protected ApiSubscriberService $apiService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        ApiSubscriberService $apiService
    ) {
        $this->subscribers = $subscribers;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscribers = $this->subscribers->paginate($workspaceId, 'last_name');

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberStoreRequest $request): SubscriberResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscriber = $this->apiService->storeOrUpdate($workspaceId, collect($request->validated()));

        $subscriber->load('tags');

        return new SubscriberResource($subscriber);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): SubscriberResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();

        return new SubscriberResource($this->subscribers->find($workspaceId, $id, ['tags']));
    }

    /**
     * @throws Exception
     */
    public function update(SubscriberUpdateRequest $request, int $id): SubscriberResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $subscriber = $this->subscribers->update($workspaceId, $id, $request->validated());

        return new SubscriberResource($subscriber);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): Response
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $this->apiService->delete($workspaceId, $this->subscribers->find($workspaceId, $id));

        return response(null, 204);
    }
}
