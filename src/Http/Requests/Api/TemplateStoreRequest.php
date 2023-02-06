<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Cornatul\Marketing\Base\Facades\MarketingPortal;

class TemplateStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sendportal_templates')
                    ->where('workspace_id', MarketingPortal::currentWorkspaceId()),
            ],
            'content' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('The template name must be unique.'),
        ];
    }
}
