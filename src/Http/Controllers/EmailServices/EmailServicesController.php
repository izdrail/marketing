<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers\EmailServices;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\EmailServiceRequest;
use Cornatul\Marketing\Base\Repositories\EmailServiceTenantRepository;

class EmailServicesController extends Controller
{
    /** @var EmailServiceTenantRepository */
    private EmailServiceTenantRepository $emailServices;

    public function __construct(EmailServiceTenantRepository $emailServices)
    {
        $this->emailServices = $emailServices;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $emailServices = $this->emailServices->all(MarketingPortal::currentWorkspaceId());

        return view('marketing::email_services.index', compact('emailServices'));
    }

    public function create(): View
    {
        $emailServiceTypes = $this->emailServices->getEmailServiceTypes()->pluck('name', 'id');

        return view('marketing::email_services.create', compact('emailServiceTypes'));
    }

    /**
     * @throws Exception
     */
    public function store(EmailServiceRequest $request): RedirectResponse
    {
        $emailServiceType = $this->emailServices->findType($request->type_id);

        $settings = $request->get('settings', []);

        $this->emailServices->store(MarketingPortal::currentWorkspaceId(), [
            'name' => $request->name,
            'type_id' => $emailServiceType->id,
            'settings' => $settings,
        ]);

        return redirect()->route('marketing.email_services.index');
    }

    /**
     * @throws Exception
     */
    public function edit(int $emailServiceId)
    {
        $emailServiceTypes = $this->emailServices->getEmailServiceTypes()->pluck('name', 'id');
        $emailService = $this->emailServices->find(MarketingPortal::currentWorkspaceId(), $emailServiceId);
        $emailServiceType = $this->emailServices->findType($emailService->type_id);

        return view('marketing::email_services.edit', compact('emailServiceTypes', 'emailService', 'emailServiceType'));
    }

    /**
     * @throws Exception
     */
    public function update(EmailServiceRequest $request, int $emailServiceId): RedirectResponse
    {
        $emailService = $this->emailServices->find(MarketingPortal::currentWorkspaceId(), $emailServiceId, ['type']);

        $settings = $request->get('settings');

        $emailService->name = $request->name;
        $emailService->settings = $settings;
        $emailService->save();

        return redirect()->route('marketing.email_services.index');
    }

    /**
     * @throws Exception
     */
    public function delete(int $emailServiceId): RedirectResponse
    {
        $emailService = $this->emailServices->find(MarketingPortal::currentWorkspaceId(), $emailServiceId, ['campaigns']);

        if ($emailService->in_use) {
            return redirect()->back()->withErrors(__("You cannot delete an email service that is currently used by a campaign or automation."));
        }

        $this->emailServices->destroy(MarketingPortal::currentWorkspaceId(), $emailServiceId);

        return redirect()->route('marketing.email_services.index');
    }

    public function emailServicesTypeAjax($emailServiceTypeId): JsonResponse
    {
        $emailServiceType = $this->emailServices->findType($emailServiceTypeId);

        $view = view()
            ->make('marketing::email_services.options.' . strtolower($emailServiceType->name))
            ->render();

        return response()->json([
            'view' => $view
        ]);
    }
}
