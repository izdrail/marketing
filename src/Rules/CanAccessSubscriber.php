<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Rules;

use Illuminate\Contracts\Validation\Rule;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Subscriber;

class CanAccessSubscriber implements Rule
{
    public function passes($attribute, $value): bool
    {
        $subscriber = Subscriber::find($value);

        if (!$subscriber) {
            return false;
        }
        //@todo check this return value
        return $subscriber->workspace_id == MarketingPortal::currentWorkspaceId();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
