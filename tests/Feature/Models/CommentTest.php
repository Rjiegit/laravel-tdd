<?php

namespace Tests\Feature\Models;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tests\TestCase;


class CommentTest extends TestCase
{

    public function test_a_comment_has_morph_to_attribute()
    {
        $comment = Comment::factory()->create();

        $this->assertInstanceOf(MorphTo::class, $comment->commented());
    }

    public function test_a_comment_belongs_to_an_owner()
    {
        $comment = Comment::factory()->create();

        $this->assertInstanceOf(BelongsTo::class, $comment->owner());
        $this->assertInstanceOf(User::class, $comment->owner);
    }
}
