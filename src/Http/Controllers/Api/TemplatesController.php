<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\Api\TemplateStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\Api\TemplateUpdateRequest;
use Cornatul\Marketing\Base\Http\Resources\Template as TemplateResource;
use Cornatul\Marketing\Base\Repositories\TemplateTenantRepository;
use Cornatul\Marketing\Base\Services\Templates\TemplateService;

class TemplatesController extends Controller
{
    /** @var TemplateTenantRepository */
    private TemplateTenantRepository $templates;

    /** @var TemplateService */
    private TemplateService $service;

    public function __construct(TemplateTenantRepository $templates, TemplateService $service)
    {
        $this->templates = $templates;
        $this->service = $service;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $templates = $this->templates->paginate($workspaceId);

        return TemplateResource::collection($templates);
    }


    /**
     * @throws Exception
     */
    public function show(int $id): TemplateResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();

        return new TemplateResource($this->templates->find($workspaceId, $id));
    }

    /**
     * @throws Exception
     */
    public function store(TemplateStoreRequest $request): TemplateResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $template = $this->service->store($workspaceId, $request->validated());

        return new TemplateResource($template);
    }

    /**
     * @throws Exception
     */
    public function update(TemplateUpdateRequest $request, int $id): TemplateResource
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $template = $this->service->update($workspaceId, $id, $request->validated());

        return new TemplateResource($template);
    }

    /**
     * @throws \Throwable
     */
    public function destroy(int $id): Response
    {
        $workspaceId = MarketingPortal::currentWorkspaceId();
        $this->service->delete($workspaceId, $id);

        return response(null, 204);
    }
}
