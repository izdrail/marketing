<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\EmailService;
use Cornatul\Marketing\Base\Models\EmailServiceType;

class EmailServiceFactory extends Factory
{
    /** @var string */
    protected $model = EmailService::class;

    public function definition(): array
    {
        return [
            'name' => ucwords($this->faker->word),
            'workspace_id' => MarketingPortal::currentWorkspaceId(),
            'type_id' => $this->faker->randomElement(EmailServiceType::pluck('id')),
            'settings' => ['foo' => 'bar'],
        ];
    }
}
