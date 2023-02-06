<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Resources\Workspace as WorkspaceResource;
use Cornatul\Marketing\Base\Repositories\WorkspacesRepository;

class WorkspacesController extends Controller
{
    /** @var WorkspacesRepository */
    private WorkspacesRepository $workspaces;

    public function __construct(WorkspacesRepository $workspaces)
    {
        $this->workspaces = $workspaces;
    }

    /**
     * @throws Exception
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $workspaces = $this->workspaces->workspacesForUser($request->user());

        return WorkspaceResource::collection($workspaces);
    }
}
