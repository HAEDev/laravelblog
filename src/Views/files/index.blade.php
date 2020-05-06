@extends(config("laravel-blog.view_layout", "laravel-blog::layout"))

@section(config("laravel-blog.view_content", "content"))

    <div class="row header">

        <div class="col-sm-6">
            <h3>{{ config("laravel-blog.taxonomy", "Blog") }} Files</h3>
        </div> <!-- End .col-sm-6 -->

        <div class="col-sm-6 text-right" style="padding-top: 1.5rem;">
            @if(Request::get("laravel-blog-embed", false) && Request::get("laravel-blog-featured", false))
                <a href="{{ blogUrl("files/create?laravel-blog-embed=true&laravel-blog-featured=true") }}" class="btn btn-primary btn-sm">
                    Upload Files
                </a>
            @elseif(Request::get("laravel-blog-embed", false))
                <a href="{{ blogUrl("files/create?laravel-blog-embed=true") }}" class="btn btn-primary btn-sm">
                    Upload Files
                </a>
            @else
                <a href="{{ blogUrl("files/create") }}" class="btn btn-primary">
                    Upload Files
                </a>
            @endif
        </div>

    </div> <!-- End .row -->

    <div class="row">
        <div class="col-sm-12">

            @include("laravel-blog::actions")

            <div class="files-flex">
                @forelse($files as $file)
                    <div class="file">

                        @if(Request::get("laravel-blog-embed", false))
                            <a href="{{ $file->getUrl() }}" target="_blank" class="ck-select-file"
                               data-url="{{ $file->getUrl() }}" data-alt-text="{{ $file->alt_text }}">
                                <img src="{{ $file->getUrl() }}" alt="">
                            </a>
                        @else
                            <a href="{{ $file->getUrl() }}" target="_blank">
                                <img src="{{ $file->getUrl() }}" alt="">
                            </a>
                        @endif

                        @if(Request::get("laravel-blog-featured", false))
                            <div class="actions text-center">
                                <button class="btn btn-xs btn-primary select-featured" data-id="{{ $file->id }}"
                                    data-url="{{ $file->getUrl() }}" data-alt-text="{{ $file->alt_text }}">
                                    Select
                                </button>
                            </div> <!-- End .actions.text-center -->
                        @elseif(Request::get("laravel-blog-embed", false))
                            <div class="actions text-center">
                                <button class="btn btn-xs btn-primary ck-select-file" data-id="{{ $file->id }}"
                                        data-url="{{ $file->getUrl() }}" data-alt-text="{{ $file->alt_text }}">
                                    Select
                                </button>
                            </div> <!-- End .actions.text-center -->
                        @else
                            <div class="actions text-right">
                                <div class="dropdown">
                                    <button class="btn btn-default btn-xs dropdown-toggle"
                                            type="button" id="dropdownMenu-{{$file->id}}"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        Actions
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu-{{$file->id}}">
                                        <li>
                                            <a href="{{ $file->getUrl() }}" target="_blank">
                                                <i class="fa fa-fw fa-eye"></i>View
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ blogUrl("files/$file->id/edit") }}">
                                                <i class="fa fa-fw fa-pencil"></i>Edit
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="copy-url"><i class="fa fa-fw fa-copy"></i>Copy Url</a>
                                            <input type="text" value="{{ $file->getUrl() }}" />
                                        </li>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <form action="{{ blogUrl("files/$file->id") }}" method="post" class="form-inline confirm-delete">
                                                {{ csrf_field() }} {{ method_field("DELETE") }}
                                                <button class="dropdown-button"><i class="fa fa-fw fa-trash"></i>Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div> <!-- End .actions.text-right -->
                        @endif

                    </div>
                @empty
                    <div class="text-muted">
                        No files uploaded yet
                    </div>
                @endforelse
            </div>

            @if($files)
                <div class="text-right">
                    {{ $files->appends([
                        'laravel-blog-embed' => Request::get("laravel-blog-embed", false) ? "true" : "",
                        'laravel-blog-featured' => Request::get("laravel-blog-featured", false) ? "true" : ""
                    ])->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
