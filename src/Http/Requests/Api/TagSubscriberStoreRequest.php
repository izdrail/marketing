<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Cornatul\Marketing\Base\Rules\CanAccessSubscriber;

class TagSubscriberStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subscribers' => ['array', 'required'],
            'subscribers.*' => ['integer', new CanAccessSubscriber()]
        ];
    }
}
