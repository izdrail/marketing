<?php

namespace Cornatul\Marketing\Base\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Cornatul\Marketing\Base\Adapters\BaseMailAdapter;
use Cornatul\Marketing\Base\Factories\MailAdapterFactory;
use Cornatul\Marketing\Base\Interfaces\QuotaServiceInterface;
use Cornatul\Marketing\Base\Models\EmailService;
use Cornatul\Marketing\Base\Models\EmailServiceType;

class QuotaService implements QuotaServiceInterface
{
    public function exceedsQuota(EmailService $emailService, int $messageCount): bool
    {
        switch ($emailService->type_id) {
            case EmailServiceType::SES:
                return $this->exceedsSesQuota($emailService, $messageCount);

            case EmailServiceType::SENDGRID:
            case EmailServiceType::MAILGUN:
            case EmailServiceType::POSTMARK:
            case EmailServiceType::MAILJET:
            case EmailServiceType::SMTP:
            case EmailServiceType::POSTAL:
                return false;
        }

        throw new \DomainException('Unrecognised email service type');
    }

    protected function resolveMailAdapter(EmailService $emailService): BaseMailAdapter
    {
        return app(MailAdapterFactory::class)->adapter($emailService);
    }

    protected function exceedsSesQuota(EmailService $emailService, int $messageCount): bool
    {
        $mailAdapter = $this->resolveMailAdapter($emailService);

        $quota = $mailAdapter->getSendQuota();

        if (empty($quota)) {
            Log::error(
                'Failed to fetch quota from SES',
                [
                    'email_service_id' => $emailService->id,
                ]
            );

            return false;
        }

        $limit = Arr::get($quota, 'Max24HourSend');

        // -1 signifies an unlimited quota
        if ($limit === -1) {
            return false;
        }

        $sent = Arr::get($quota, 'SentLast24Hours');

        $remaining = (int)floor($limit - $sent);

        return $messageCount > $remaining;
    }
}
