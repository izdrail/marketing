<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Message;
use Cornatul\Marketing\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Cornatul\Marketing\Base\Services\Content\MergeContentService;
use Cornatul\Marketing\Base\Services\Content\MergeSubjectService;
use Cornatul\Marketing\Base\Services\Messages\DispatchMessage;

class MessagesController extends Controller
{
    /** @var MessageTenantRepositoryInterface */
    protected MessageTenantRepositoryInterface $messageRepo;

    /** @var DispatchMessage */
    protected DispatchMessage $dispatchMessage;

    /** @var MergeContentService */
    protected MergeContentService $mergeContentService;

    /** @var MergeSubjectService */
    protected MergeSubjectService $mergeSubjectService;

    public function __construct(
        MessageTenantRepositoryInterface $messageRepo,
        DispatchMessage $dispatchMessage,
        MergeContentService $mergeContentService,
        MergeSubjectService $mergeSubjectService
    ) {
        $this->messageRepo = $messageRepo;
        $this->dispatchMessage = $dispatchMessage;
        $this->mergeContentService = $mergeContentService;
        $this->mergeSubjectService = $mergeSubjectService;
    }

    /**
     * Show all sent messages.
     *
     * @throws Exception
     */
    public function index(): View
    {
        $params = request()->only(['search', 'status']);
        $params['sent'] = true;

        $messages = $this->messageRepo->paginateWithSource(
            MarketingPortal::currentWorkspaceId(),
            'sent_atDesc',
            [],
            50,
            $params
        );

        return view('marketing::messages.index', compact('messages'));
    }

    /**
     * Show draft messages.
     *
     * @throws Exception
     */
    public function draft(): View
    {
        $messages = $this->messageRepo->paginateWithSource(
            MarketingPortal::currentWorkspaceId(),
            'created_atDesc',
            [],
            50,
            ['draft' => true]
        );

        return view('marketing::messages.index', compact('messages'));
    }

    /**
     * Show a single message.
     *
     * @throws Exception
     */
    public function show(int $messageId): View
    {
        $message = $this->messageRepo->find(MarketingPortal::currentWorkspaceId(), $messageId);

        $content = $this->mergeContentService->handle($message);
        $subject = $this->mergeSubjectService->handle($message);

        return view('marketing::messages.show', compact('content', 'message', 'subject'));
    }

    /**
     * Send a message.
     *
     * @throws Exception
     */
    public function send(): RedirectResponse
    {
        if (!$message = $this->messageRepo->find(
            MarketingPortal::currentWorkspaceId(),
            request('id'),
            ['subscriber']
        )) {
            return redirect()->back()->withErrors(__('Unable to locate that message'));
        }

        if ($message->sent_at) {
            return redirect()->back()->withErrors(__('The selected message has already been sent'));
        }

        $this->dispatchMessage->handle($message);

        return redirect()->route('marketing.messages.draft')->with(
            'success',
            __('The message was sent successfully.')
        );
    }

    /**
     * Send a message.
     *
     * @throws Exception
     */
    public function delete(): RedirectResponse
    {
        if (!$message = $this->messageRepo->find(
            MarketingPortal::currentWorkspaceId(),
            request('id')
        )) {
            return redirect()->back()->withErrors(__('Unable to locate that message'));
        }

        if ($message->sent_at) {
            return redirect()->back()->withErrors(__('A sent message cannot be deleted'));
        }

        $this->messageRepo->destroy(
            MarketingPortal::currentWorkspaceId(),
            $message->id
        );

        return redirect()->route('marketing.messages.draft')->with(
            'success',
            __('The message was deleted')
        );
    }

    /**
     * Send multiple messages.
     *
     * @throws Exception
     */
    public function sendSelected(): RedirectResponse
    {
        if (! request()->has('messages')) {
            return redirect()->back()->withErrors(__('No messages selected'));
        }

        if (!$messages = $this->messageRepo->getWhereIn(
            MarketingPortal::currentWorkspaceId(),
            request('messages'),
            ['subscriber']
        )) {
            return redirect()->back()->withErrors(__('Unable to locate messages'));
        }

        $messages->each(function (Message $message) {
            if ($message->sent_at) {
                return;
            }

            $this->dispatchMessage->handle($message);
        });

        return redirect()->route('marketing.messages.draft')->with(
            'success',
            __('The messages were sent successfully.')
        );
    }
}
