<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'product_name' => $this->product_name,
            'image' => $this->image,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'manufacturer_id' => $this->manufacturer_id,
            'manufacturer_name' => $this->manufacturer_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
