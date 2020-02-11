<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use Notifiable;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function($user) {
            if ($password = Arr::get($user->getChanges(), 'password')) {
                $user->storeCurrentPasswordInHistory($user->password);
            }
        });

        self::created(function($user) {
            $user->storeCurrentPasswordInHistory($user->password);
        });
    }

    protected function storeCurrentPasswordInHistory($password)
    {
        $this->passwordHistory()->create(compact('password'));
    }

    public function passwordHistory()
    {
        return $this->hasMany(PasswordHistory::class)
            ->latest();
    }
}
