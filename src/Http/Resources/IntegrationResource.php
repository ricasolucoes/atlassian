<?php

namespace Atlassian\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    /**
     * @var true[]
     *
     * @psalm-var array{success: true}
     */
    public array $with = [
        'success' => true
    ];

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return (mixed|string)[]
     *
     * @psalm-return array{token: mixed, created_at: string, updated_at: string}
     */
    public function toArray($request)
    {
        return [
            'token' => $this->token,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at
        ];
    }
}
