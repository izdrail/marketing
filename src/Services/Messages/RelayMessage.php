<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Services\Messages;

use Exception;
use Cornatul\Marketing\Base\Factories\MailAdapterFactory;
use Cornatul\Marketing\Base\Models\EmailService;

class RelayMessage
{
    /** @var MailAdapterFactory */
    protected MailAdapterFactory $mailAdapter;

    public function __construct(MailAdapterFactory $mailAdapter)
    {
        $this->mailAdapter = $mailAdapter;
    }

    /**
     * Dispatch the email via the email service.
     *
     * @throws Exception
     */
    public function handle(string $mergedContent, MessageOptions $messageOptions, EmailService $emailService): string
    {
        return $this->mailAdapter->adapter($emailService)
            ->send(
                $messageOptions->getFromEmail(),
                $messageOptions->getFromName(),
                $messageOptions->getTo(),
                $messageOptions->getSubject(),
                $messageOptions->getTrackingOptions(),
                $mergedContent
            );
    }
}
