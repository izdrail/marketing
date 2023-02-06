<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Repositories\Subscribers;

use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Cornatul\Marketing\Base\Interfaces\BaseTenantInterface;
use Cornatul\Marketing\Base\Models\Subscriber;

interface SubscriberTenantRepositoryInterface extends BaseTenantInterface
{
    public function syncTags(Subscriber $subscriber, array $tags = []);

    public function countActive($workspaceId): int;

    public function getRecentSubscribers(int $workspaceId): Collection;

    public function getGrowthChartData(CarbonPeriod $period, int $workspaceId): array;
}
