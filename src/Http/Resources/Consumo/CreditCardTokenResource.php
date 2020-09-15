<?php

namespace Atlassian\Http\Resources\Consumo;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardTokenResource extends JsonResource
{
    public $with = [
        'success' => true
    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->creditCard->id,
            'card_id' => $this->creditCard->id,
            'brand_id' => $this->creditCard->brand_id,
            'card_number' => $this->creditCard->card_number,
            'exp_year' => $this->creditCard->exp_year,
            'exp_month' => $this->creditCard->exp_month,
            'card_name' => $this->creditCard->card_name,
            'is_active' => $this->creditCard->is_active,
            'token' => $this->company_token,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}
