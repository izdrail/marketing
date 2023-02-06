<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Cornatul\Marketing\Base\Rules\CanAccessTag;

class SubscriberTagStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tags' => ['array', 'required'],
            'tags.*' => ['integer', new CanAccessTag()]
        ];
    }
}
