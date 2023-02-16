<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $discount_final = $this->discountInPercent ? (($this->discount / 100) * $this->sellingPrice) : $this->discount;
        return [
            "id" =>$this->id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "name" => $this->name,
            "store_id" => $this->store_id,
            "hsn" => $this->hsn,
            "bar" => $this->bar,
            "unit" => $this->unit,
            "sellingPrice" => $this->sellingPrice,
            "tax" => $this->tax,
            "costPrice" => $this->costPrice,
            "discount" => $discount_final,
            "quantity" => $this->quantity,
            
            "deleted_at" => $this->deleted_at,
        ];
    }
}
