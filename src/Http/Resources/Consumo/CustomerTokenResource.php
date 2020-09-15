<?php

namespace Atlassian\Http\Resources\Consumo;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTokenResource extends JsonResource
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
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'nome' => $this->customer->name,
            'cpf' => $this->customer->cpf,
            'user_token' => $this->token,
            'token' => $this->company_token,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}
