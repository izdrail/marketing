<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Template;

class TemplateFactory extends Factory
{
    /** @var string */
    protected $model = Template::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'content' => '{{content}}'
        ];
    }
}
