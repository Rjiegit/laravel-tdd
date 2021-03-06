<?php

namespace App\Models;

use App\Models\Traits\ActiveUserHelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use ActiveUserHelperTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'userAvatar',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function getUserAvatarAttribute()
    {
        return $this->avatar();
    }

    public function avatar()
    {
        if ($this->avatar_path) {
            $path = $this->avatar_path;
        } else {
            $path = 'avatars/default.png';
        }

        return asset('storage/' . $path);
    }
}
