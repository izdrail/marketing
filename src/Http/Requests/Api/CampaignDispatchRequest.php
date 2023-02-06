<?php

declare(strict_types=1);

namespace Cornatul\Marketing\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Cornatul\Marketing\Base\Facades\MarketingPortal;
use Cornatul\Marketing\Base\Models\Campaign;
use Cornatul\Marketing\Base\Models\CampaignStatus;
use Cornatul\Marketing\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;

class CampaignDispatchRequest extends FormRequest
{
    /**
     * @var CampaignTenantRepositoryInterface
     */
    protected CampaignTenantRepositoryInterface $campaigns;

    /**
     * @var Campaign
     */
    protected Campaign $campaign;

    public function __construct(CampaignTenantRepositoryInterface $campaigns)
    {
        parent::__construct();

        $this->campaigns = $campaigns;

        Validator::extendImplicit('valid_status', function ($attribute, $value, $parameters, $validator) {
            return $this->getCampaign()->status_id === CampaignStatus::STATUS_DRAFT;
        });
    }

    /**
     * @param array $relations
     * @return Campaign
     * @throws \Exception
     */
    public function getCampaign(array $relations = []): Campaign
    {
        return $this->campaign = $this->campaigns->find(MarketingPortal::currentWorkspaceId(), $this->id, $relations);
    }

    public function rules()
    {
        return [
            'status_id' => 'valid_status',
        ];
    }

    public function messages(): array
    {
        return [
            'valid_status' => __('The campaign must have a status of draft to be dispatched'),
        ];
    }
}
