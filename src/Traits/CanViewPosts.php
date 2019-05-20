<?php

namespace Lnch\LaravelBlog\Traits;

use Lnch\LaravelBlog\Models\BlogPost;

trait CanViewPosts
{
    public function hasRead($post)
    {
        if (!$post instanceof BlogPost) {
            $post = BlogPost::findOrFail($post);
        }

        return $post->views()->where(['user_id' => $this->id])->exists();
    }
}
