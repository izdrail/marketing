<?php

declare(strict_types=1);

namespace Tests\Feature\Tags;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Subscriber;
use Cornatul\Marketing\Base\Models\Tag;
use Tests\TestCase;

class TagsControllerTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    /** @test */
    public function the_index_of_tags_is_accessible_to_authenticated_users()
    {
        // given
        Tag::factory()->count(3)->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        // when
        $response = $this->get(route('marketing.tags.index'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function the_tag_create_form_is_accessible_to_authenticated_users()
    {
        // when
        $response = $this->get(route('marketing.tags.create'));

        // then
        $response->assertOk();
    }

    /** @test */
    public function new_tags_can_be_created_by_authenticated_users()
    {
        // given
        $tagStoreData = [
            'name' => $this->faker->word
        ];

        // when
        $response = $this
            ->post(route('marketing.tags.store'), $tagStoreData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('sendportal_tags', [
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'name' => $tagStoreData['name']
        ]);
    }

    /** @test */
    public function the_tag_edit_view_is_accessible_by_authenticated_users()
    {
        // given
        $tag = Tag::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        // when
        $response = $this->get(route('marketing.tags.edit', $tag->id));

        // then
        $response->assertOk();
    }

    /** @test */
    public function a_tag_is_updateable_by_an_authenticated_user()
    {
        // given
        $tag = Tag::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        $tagUpdateData = [
            'name' => $this->faker->word
        ];

        // when
        $response = $this
            ->put(route('marketing.tags.update', $tag->id), $tagUpdateData);

        // then
        $response->assertRedirect();
        $this->assertDatabaseHas('sendportal_tags', [
            'id' => $tag->id,
            'name' => $tagUpdateData['name']
        ]);
    }

    /** @test */
    public function subscribers_are_not_synced_when_the_tag_is_updated()
    {
        // given
        $subscribers = Subscriber::factory()->count(5)->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
        ]);

        $tag = Tag::factory()->create([
            'workspace_id' => MarketingPortal::currentWorkspaceId()
        ]);

        $tag->subscribers()->attach($subscribers);

        self::assertCount($subscribers->count(), $tag->subscribers);

        // when
        $this->put(route('marketing.tags.update', $tag->id), [
            'name' => 'Very Cool New Name',
        ]);

        $tag->refresh();

        // then
        self::assertCount($subscribers->count(), $tag->subscribers);
    }


    /* @test */
    public function a_tag_can_be_deleted()
    {
        // given
        $tag = Tag::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        // when
        $response = $this
            ->delete(route('marketing.tags.destroy', $tag->id));

        // then
        $response->assertRedirect();

        $this->assertDatabaseMissing('sendportal_tags', [
            'id' => $tag->id,
        ]);
    }

    /** @test */
    public function a_tag_name_must_be_unique_for_a_workspace()
    {
        // given
        $tag = Tag::factory()->create(['workspace_id' => MarketingPortal::currentWorkspaceId()]);

        $request = [
            'name' => $tag->name,
        ];

        // when
        $response = $this->post(route('marketing.tags.store'), $request);

        // then
        $response->assertRedirect()
            ->assertSessionHasErrors('name');

        self::assertEquals(1, Tag::where('name', $tag->name)->count());
    }
}
