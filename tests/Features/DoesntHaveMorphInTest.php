<?php

use LaravelReady\Hasin\Tests\Models\Comment;
use LaravelReady\Hasin\Tests\Models\Post;

test('doesntHaveMorphIn same as doesntHaveMorph', function () {
    $doesntHaveMorph = Comment::doesntHaveMorph('commentable', [Post::class])->orderBy('id')->pluck('id');
    $doesntHaveMorphIn = Comment::doesntHaveMorphIn('commentable', [Post::class])->orderBy('id')->pluck('id');

    expect($doesntHaveMorph)->toEqual($doesntHaveMorphIn);
});
