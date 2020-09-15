<?php

namespace Atlassian\Http\Resources\Consumo;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditCardResource extends JsonResource
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
            'id' => $this->id,
            'card_id' => $this->id,
            'brand_id' => $this->brand_id,
            'card_number' => $this->card_number,
            'exp_year' => $this->exp_year,
            'exp_month' => $this->exp_month,
            'card_name' => $this->card_name,
            'is_active' => $this->is_active,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
        ];
    }
}
