<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Tags;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\TagStoreRequest;
use Cornatul\Marketing\Base\Http\Requests\TagUpdateRequest;
use Cornatul\Marketing\Base\Repositories\TagTenantRepository;

class TagsController extends Controller
{
    /** @var TagTenantRepository */
    private TagTenantRepository $tagRepository;

    public function __construct(TagTenantRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $tags = $this->tagRepository->paginate(MarketingPortal::currentWorkspaceId(), 'name');

        return view('marketing::tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('marketing::tags.create');
    }

    /**
     * @throws Exception
     */
    public function store(TagStoreRequest $request): RedirectResponse
    {
        $this->tagRepository->store(MarketingPortal::currentWorkspaceId(), $request->all());

        return redirect()->route('marketing.tags.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $id): View
    {
        $tag = $this->tagRepository->find(MarketingPortal::currentWorkspaceId(), $id, ['subscribers']);

        return view('marketing::tags.edit', compact('tag'));
    }

    /**
     * @throws Exception
     */
    public function update(int $id, TagUpdateRequest $request): RedirectResponse
    {
        $this->tagRepository->update(MarketingPortal::currentWorkspaceId(), $id, $request->all());

        return redirect()->route('marketing.tags.index');
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->tagRepository->destroy(MarketingPortal::currentWorkspaceId(), $id);

        return redirect()->route('marketing.tags.index');
    }
}
