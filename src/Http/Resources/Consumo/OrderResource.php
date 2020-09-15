<?php

namespace Atlassian\Http\Resources\Consumo;

use Illuminate\Http\Resources\Json\JsonResource;
use Population\Models\Order;

class OrderResource extends JsonResource
{
    public $with = [
        'success' => true
    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // @todo Descobrir CAmpo CreditCard e Brand_id e preencher
    public function toArray($request)
    {
        return [
            'nsu' => $this->nsu,
            'tid' => $this->tid,
            'status' => $this->getStatusName($this->status),
            'brand_id' => '',
            'credit_card' => $this->credit_card_id,
            'installments' => $this->installments,
            'total' => $this->total,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }

    protected function getStatusName($statusCode)
    {
        // @todo Descobrir todos os códigos

        if ($statusCode == Order::$STATUS_APPROVED) {
            return 'approved';
        }

        if ($statusCode == '6') {
            return 'review';
        }

        if ($statusCode == '7') {
            return 'declined';
        }

        if ($statusCode == '8') {
            return 'nao sei o nome'; // @todo Descobrir nome do Status
        }

        return 'paid';
    }
}
