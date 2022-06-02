<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'store_id' => $this->store_id,
            'delivery_date' => $this->delivery_date,
            'product_count' => $this->product_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product_name' => $this->product_name,
            'store_name' => $this->store_name,
        ];
    }
}
