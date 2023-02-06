<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Workspaces;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Cornatul\Marketing\Base\Models\Workspace;

class SwitchWorkspaceController
{
    public function switch(Request $request, Workspace $workspace): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->onWorkspace($workspace), 404);

        $user->switchToWorkspace($workspace);

        return redirect()->route('marketing.dashboard');
    }
}
