<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "display_name" => $this->display_name,
            "group"        => $this->group,
            "description"  => $this->description,
            "created_at"   => $this->created_at,
            "created_by"   => $this->whenLoaded('createdBy')
        ];
    }
}
