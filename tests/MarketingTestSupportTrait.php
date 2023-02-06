<?php

declare(strict_types=1);

namespace Tests;

use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\EmailService;
use Cornatul\Marketing\Base\Models\Subscriber;
use Cornatul\Marketing\Base\Models\Tag;

//@todo rename this to marketing support test
trait MarketingTestSupportTrait
{
    protected function createEmailService(): EmailService
    {
        return EmailService::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
        ]);
    }

    protected function createCampaign(EmailService $emailService): Campaign
    {
        return Campaign::factory()
            ->withContent()
            ->sent()
            ->create([
                'workspace_id' => MarketingPortal::currentWorkspaceId(),
                'email_service_id' => $emailService->id,
            ]);
    }

    protected function createTag(): Tag
    {
        return Tag::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
        ]);
    }

    protected function createSubscriber(): Subscriber
    {
        return Subscriber::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
        ]);
    }
}
