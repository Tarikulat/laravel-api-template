<?php

namespace App\Classes;

use App\Models\User;
use Illuminate\Support\Str;
use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends Model
{
    use HasFactory, Userstamps;

    protected $guarded = ["id"];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function scopeActive(): void
    {
        $this->where('status', 'active');
    }

    // Generate slug
    protected function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: fn(string $value) => Str::slug($value),
        );
    }

    public function getOldPath($filePath)
    {
        return public_path($filePath);
    }
}
