<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit' => $this->unit,
            'value' => $this->whenPivotLoaded('attribute_product', function () {
                return $this->pivot->value;
            }),
        ];
    }
}
