<?php

namespace App\Models;

use App\Models\Traits\InvitedUsersTrait;
use App\Models\Traits\VoteTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    use VoteTrait;
    use InvitedUsersTrait;

    protected $guarded = ['id'];
    protected $with = ['owner'];

    protected $appends = [
        'upVotesCount',
        'downVotesCount',
    ];

    public function commented()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
