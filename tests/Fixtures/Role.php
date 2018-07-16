<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Get the users that the role belongs to.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
                            ->withPivot('id', 'admin', 'photo', 'restricted')
                            ->using(RoleAssignment::class);
    }
}
