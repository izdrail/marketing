<?php


namespace Cornatul\Marketing\Base\Http\Controllers\EmailServices;

use Exception;
use Illuminate\Http\RedirectResponse;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Http\Controllers\Controller;
use Cornatul\Marketing\Base\Http\Requests\EmailServiceTestRequest;
use Cornatul\Marketing\Base\Repositories\EmailServiceTenantRepository;
use Cornatul\Marketing\Base\Services\Messages\DispatchTestMessage;
use Cornatul\Marketing\Base\Services\Messages\MessageOptions;

class TestEmailServiceController extends Controller
{
    /** @var EmailServiceTenantRepository */
    private EmailServiceTenantRepository $emailServices;

    public function __construct(EmailServiceTenantRepository $emailServices)
    {
        $this->emailServices = $emailServices;
    }

    public function create(int $emailServiceId)
    {
        $emailService = $this->emailServices->find(MarketingPortal::currentWorkspaceId(), $emailServiceId);

        return view('marketing::email_services.test', compact('emailService'));
    }

    /**
     * @throws Exception
     */
    public function store(int $emailServiceId, EmailServiceTestRequest $request, DispatchTestMessage $dispatchTestMessage): RedirectResponse
    {
        $emailService = $this->emailServices->find(MarketingPortal::currentWorkspaceId(), $emailServiceId);

        $options = new MessageOptions();
        $options->setFromEmail($request->input('from'));
        $options->setSubject($request->input('subject'));
        $options->setTo($request->input('to'));
        $options->setBody($request->input('body'));

        try {
            $messageId = $dispatchTestMessage->testService(MarketingPortal::currentWorkspaceId(), $emailService, $options);

            if (!$messageId) {
                return redirect()
                    ->back()
                    ->with(['error', __('Failed to dispatch test email.')]);
            }

            return redirect()
                ->route('marketing.email_services.index')
                ->with(['success' => __('The test email has been dispatched.')]);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Response: ' . $e->getMessage());
        }
    }
}
