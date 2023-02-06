<?php

namespace Cornatul\Marketing\Base\Interfaces;

use Cornatul\Marketing\Base\Models\EmailService;

interface QuotaServiceInterface
{
    public function exceedsQuota(EmailService $emailService, int $messageCount): bool;
}
