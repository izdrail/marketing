<?php

declare(strict_types=1);

namespace Tests\Feature\Content;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\Message;
use Cornatul\Marketing\Base\Models\Subscriber;
use Cornatul\Marketing\Base\Services\Content\MergeSubjectService;
use Tests\TestCase;

class MergeSubjectTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function the_email_tag_is_replaced_with_the_subscriber_email()
    {
        // given
        $subject = 'Hi, {{email}}';
        $message = $this->generateCampaignMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, foo@bar.com', $mergedSubject);
    }

    /** @test */
    public function the_first_name_tag_is_replaced_with_the_subscriber_first_name()
    {
        // given
        $subject = 'Hi, {{first_name}}';
        $message = $this->generateCampaignMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, foo', $mergedSubject);
    }

    /** @test */
    public function the_first_name_tag_is_replaced_with_an_empty_string_if_the_subscriber_first_name_is_null()
    {
        // given
        $subject = 'Hi, {{first_name}}';
        $message = $this->generateCampaignMessage($subject, 'foo@bar.com');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, ', $mergedSubject);
    }

    /** @test */
    public function the_last_name_tag_is_replaced_with_the_subscriber_last_name()
    {
        // given
        $subject = 'Hi, {{last_name}}';
        $message = $this->generateCampaignMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, bar', $mergedSubject);
    }

    /** @test */
    public function the_last_name_tag_is_replaced_with_an_empty_string_if_the_subscriber_last_name_is_null()
    {
        // given
        $subject = 'Hi, {{last_name}}';
        $message = $this->generateCampaignMessage($subject, 'foo@bar.com');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, ', $mergedSubject);
    }

    /** @test */
    public function multiple_different_tags_are_replaced()
    {
        // given
        $subject = 'Hi, {{first_name}} {{ last_name }} ({{email }})';
        $message = $this->generateCampaignMessage($subject, 'foo@bar.com', 'foo', 'bar');

        // when
        $mergedSubject = $this->mergeSubject($message);

        // then
        self::assertEquals('Hi, foo bar (foo@bar.com)', $mergedSubject);
    }

    private function generateCampaignMessage(
        string $campaignSubject,
        string $email,
        ?string $firstName = null,
        ?string $lastName = null
    ): Message {
        /** @var Campaign $campaign */
        $campaign = Campaign::factory()->create([
            'content' => '<p>Content</p>',
            'subject' => $campaignSubject,
            'workspace_id' => MarketingPortal::currentWorkspaceId()
        ]);

        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        return Message::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'subscriber_id' => $subscriber->id,
            'source_type' => Campaign::class,
            'source_id' => $campaign->id,
            'subject' => $campaignSubject,
            'recipient_email' => $email,
        ]);
    }

    private function mergeSubject(Message $message): string
    {
        /** @var MergeSubjectService $mergeSubject */
        $mergeSubject = app(MergeSubjectService::class);

        return $mergeSubject->handle($message);
    }
}
