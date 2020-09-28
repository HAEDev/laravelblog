<?php

namespace Lnch\LaravelBlog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $siteId = getBlogSiteID();

        switch ($this->method())
        {
            case 'POST':
                $rules['tags'] = 'required|string';
                break;
            case 'PATCH':
                $id = $this->tag_id;
                $rules['tag'] = "required|string|unique:blog_tags,name,$id,id,site_id,$siteId";
                break;
        }

        return $rules;
    }
}
