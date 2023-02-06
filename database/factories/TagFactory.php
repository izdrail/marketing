<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Tag;

class TagFactory extends Factory
{
    /** @var string */
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'name' => ucwords($this->faker->unique()->word)
        ];
    }
}
