<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Requests\TemplateStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\TemplateUpdateRequest;
use Cornatul\Marketing\Base\Repositories\TemplateTenantRepository;
use Cornatul\Marketing\Base\Services\Templates\TemplateService;
use Cornatul\Marketing\Base\Traits\NormalizeTags;
use Throwable;

class TemplatesController extends Controller
{
    use NormalizeTags;

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
    public final function index(): Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        $templates = $this->templates->paginate(MarketingPortal::currentWorkspaceId());

        return view('marketing::templates.index', compact('templates'));
    }

    public function create(): View
    {
        return view('marketing::templates.create');
    }

    /**
     * @throws Exception
     */
    public function store(TemplateStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->service->store(MarketingPortal::currentWorkspaceId(), $data);

        return redirect()
            ->route('marketing.templates.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $template = $this->templates->find(MarketingPortal::currentWorkspaceId(), $id);

        return view('marketing::templates.edit', compact('template'));
    }

    /**
     * @throws Exception
     */
    public function update(TemplateUpdateRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();

        $this->service->update(MarketingPortal::currentWorkspaceId(), $id, $data);

        return redirect()
            ->route('marketing.templates.index');
    }

    /**
     * @throws Throwable
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete(MarketingPortal::currentWorkspaceId(), $id);

        return redirect()
            ->route('marketing.templates.index')
            ->with('success', __('Template successfully deleted.'));
    }



    public final function import(): Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
    {
        return view('marketing::templates.import');
    }
}
