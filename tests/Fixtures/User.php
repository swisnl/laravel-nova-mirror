<?php

namespace Laravel\Nova\Tests\Fixtures;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The password reset token that was last issued.
     *
     * @var string
     */
    public static $passwordResetToken;

    /**
     * Get the first of the addresses that belong to the user.
     */
    public function address()
    {
        return $this->hasOne(Address::class);
    }

    /**
     * Get all of the posts that belong to the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get all of the roles assigned to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
                            ->withPivot('id', 'admin', 'photo', 'restricted')
                            ->using(RoleAssignment::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        static::$passwordResetToken = $token;

        $this->notify(new ResetPasswordNotification($token));
    }
}
