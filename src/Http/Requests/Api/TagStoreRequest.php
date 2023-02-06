<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Cornatul\Marketing\Base\Facades\MarketingPortal;

class TagStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                Rule::unique('sendportal_tags')
                    ->where('workspace_id', MarketingPortal::currentWorkspaceId()),
            ],
            'subscribers' => [
                'array',
                'nullable',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The tag name must be unique.'),
        ];
    }
}
