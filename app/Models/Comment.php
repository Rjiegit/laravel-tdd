<?php

namespace App\Models;

use App\Models\Traits\VoteTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    use VoteTrait;

    protected $guarded = ['id'];

    protected $appends = [
        'upVotesCount',
        'downVotesCount',
    ];
}
