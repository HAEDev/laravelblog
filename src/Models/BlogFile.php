<?php

namespace Lnch\LaravelBlog\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class BlogFile extends BlogModel
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'path'
    ];

    public $table = "blog_files";
}
