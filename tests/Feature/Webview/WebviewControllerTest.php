<?php

declare(strict_types=1);

namespace Tests\Feature\Webview;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\Message;
use Tests\TestCase;

class WebviewControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_message_can_be_seen_in_the_webview()
    {
        // given
        $campaign = Campaign::factory()->withContent()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);
        $message = Message::factory()->create(['source_id' => $campaign->id, 'workspace_id' => MarketingPortal::currentWorkspaceId()]);

        // when
        $response = $this->get(route('marketing.webview.show', $message->hash));

        // then
        $response->assertOk();
    }
}
