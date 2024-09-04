<?php

namespace App\Models;

use Laratrust\Models\Permission as PermissionModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends PermissionModel
{
    public $guarded = [];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
