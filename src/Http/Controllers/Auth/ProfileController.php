<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\ProfileUpdateRequest;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('marketing::profile.show');
    }

    public function edit(): View
    {
        return view('marketing::profile.edit');
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->back()->with('success', __('Your profile was updated successfully!'));
    }
}
