<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'salary' => $this->salary,
            'type' => $this->type,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
