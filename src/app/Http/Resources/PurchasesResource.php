<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchasesResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'store_id' => $this->store_id,
            'purchase_date' => $this->purchase_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'customer_name' => $this->customer_name,
            'store_name' => $this->store_name
        ];
    }
}
