<?php

namespace Lnch\LaravelBlog\Controllers;

use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    protected $viewPath;
    protected $routePrefix;
    protected $postModel;

    public function __construct()
    {
        $this->viewPath = config("laravel-blog.views_path");
        if ($this->viewPath) {
            $this->viewPath .= ".";
        }

        $this->routePrefix = config("laravel-blog.route_prefix");
        if ($this->routePrefix && substr($this->routePrefix, -1) !== "/") {
            $this->routePrefix .= "/";
        }

        $model = config('laravel-blog.post_model', BlogPost::class);
        $this->postModel = new $model;
    }
}
