<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductResources extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this);
        //         $discount_final = $this->discountInPercent ? (($this->discount / 100) * $this->sellingPrice) : $this->discount;


        return [
            'id' => $this->collection,

        ];
    }
}
