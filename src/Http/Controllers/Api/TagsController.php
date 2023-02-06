<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\TagStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\TagUpdateRequest;
use Cornatul\Marketing\Base\Http\Resources\Tag as TagResource;
use Cornatul\Marketing\Base\Repositories\TagTenantRepository;
use Cornatul\Marketing\Base\Services\Tags\ApiTagService;

class TagsController extends Controller
{
    /** @var TagTenantRepository */
    private TagTenantRepository $tags;

    /** @var ApiTagService */
    private ApiTagService $apiService;

    public function __construct(
        TagTenantRepository $tags,
        ApiTagService $apiService
    ) {
        $this->tags = $tags;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();

        return TagResource::collection(
            $this->tags->paginate($workspaceId, 'name', [], request()->get('per_page', 25))
        );
    }

    /**
     * @throws Exception
     */
    public function store(TagStoreRequest $request): TagResource
    {
        $input = $request->validated();
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $tag = $this->apiService->store($workspaceId, collect($input));

        $tag->load('subscribers');

        return new TagResource($tag);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): TagResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();

        return new TagResource($this->tags->find($workspaceId, $id));
    }

    /**
     * @throws Exception
     */
    public function update(TagUpdateRequest $request, int $id): TagResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $tag = $this->tags->update($workspaceId, $id, $request->validated());

        return new TagResource($tag);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): Response
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $this->tags->destroy($workspaceId, $id);

        return response(null, 204);
    }
}
