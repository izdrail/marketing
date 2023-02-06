<?php

declare(strict_types=1);

namespace Tests\Feature\EmailServices;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\EmailService;
use Cornatul\Marketing\Base\Models\EmailServiceType;
use Tests\TestCase;

class EmailServicesControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function the_provider_create_form_is_accessible_to_authenticated_users()
    {
        // when
        $response = $this->get(route('marketing.email_services.create'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function new_email_services_can_be_created_by_authenticated_users()
    {
        // given
        $emailServiceStoreData = [
            'name' => $this->faker->word,
            'type_id' => EmailServiceType::POSTMARK,
            'settings' => [
                'key' => Str::random()
            ]
        ];

        // when
        $response = $this
            ->post(route('marketing.email_services.store'), $emailServiceStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('sendportal_email_services', [
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'name' => $emailServiceStoreData['name'],
            'type_id' => $emailServiceStoreData['type_id']
        ]);
    }

    /** @test */
    public function the_email_service_edit_view_is_accessible_by_authenticated_users()
    {
        // given
        $emailService = EmailService::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        // when
        $response = $this->get(route('marketing.email_services.edit', $emailService->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function an_email_service_is_updateable_by_an_authenticated_user()
    {
        // given
        $emailService = EmailService::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        $emailServiceUpdateData = [
            'name' => $this->faker->word,
            'settings' => [
                'key' => Str::random()
            ]
        ];

        // when
        $response = $this
            ->put(route('marketing.email_services.update', $emailService->id), $emailServiceUpdateData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('sendportal_email_services', [
            'id' => $emailService->id,
            'name' => $emailServiceUpdateData['name']
        ]);
    }

    /** @test */
    public function an_email_service_can_be_deleted_by_an_authenticated_user()
    {
        // given
        $emailService = EmailService::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        // when
        $this
            ->delete(route('marketing.email_services.delete', $emailService->id));

        // then
        $this->assertDatabaseMissing('sendportal_email_services', [
            'id' => $emailService->id
        ]);
    }

    /** @test */
    public function email_services_require_the_correct_settings_to_be_saved()
    {
        // given
        $emailServiceStoreData = [
            'name' => $this->faker->word,
            'type_id' => EmailServiceType::POSTMARK,
        ];

        // when
        $response = $this
            ->post(route('marketing.email_services.store'), $emailServiceStoreData);

        // then
        $response->assertRedirect();
        $response->assertSessionHasErrors(['settings.key']);
    }

    /** @test */
    public function email_services_cannot_be_deleted_if_they_are_being_used()
    {
        $emailService = EmailService::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        Campaign::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'email_service_id' => $emailService->id
        ]);

        // when
        $response = $this
            ->delete(route('marketing.email_services.delete', $emailService->id));

        // then
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseHas('sendportal_email_services', [
            'id' => $emailService->id
        ]);
    }
}
