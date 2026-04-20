<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('permission.models.role'), config('permission.table_names.role_has_permissions'), 'permission_id', 'role_id');
    }
}
