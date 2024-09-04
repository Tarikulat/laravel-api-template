<?php

namespace App\Http\Resources\Admin;

use App\Classes\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
              "id"           => $this->id,
              "first_name"   => $this->first_name,
              "last_name"    => $this->last_name,
              "username"     => $this->username,
              "phone_number" => $this->phone_number,
              "email"        => $this->email,
              "status"       => $this->status,
              "avatar"       => Helper::getFilePath($this->file_path),
              "roles"        => RoleCollection::make($this->whenLoaded("roles")),
        ];
    }
}
