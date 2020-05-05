<?php

namespace Lnch\LaravelBlog\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lnch\LaravelBlog\Models\BlogFile;
use Illuminate\Http\UploadedFile;
use Lnch\LaravelBlog\Requests\BlogFileRequest;

class BlogFileController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (config("laravel-blog.use_auth_middleware", false)) {
            $this->middleware("auth");
        }

        if (!config("laravel-blog.files.enabled")) {
            abort(404);
        }
    }

    /**
     * Displays a full list of the active site's uploaded blog files
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if(auth()->user()->cannot("view", BlogFile::class)) {
            abort(403);
        }

        $files = BlogFile::paginate(config("laravel-blog.files.per_page"));

        return view($this->viewPath."files.index", [
            'files' => $files
        ]);
    }

    /**
     * Presents the interface to upload a new file or files
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if(auth()->user()->cannot("create", BlogFile::class)) {
            abort(403);
        }

        return view($this->viewPath."files.create");
    }

    /**
     * Handles upload of files and stores them in the database
     *
     * @param BlogFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlogFileRequest $request)
    {
        if(auth()->user()->cannot("create", BlogFile::class)) {
            abort(403);
        }

        // Upload files, create records
        foreach($request->files as $file)
        {
            DB::transaction(function() use($file, $request) {
                $this->uploadFile($file, $request);
            });
        }

        $returnUrl = blogUrl("files");
        if($request->get("laravel-blog-embed", false) && $request->get("laravel-blog-featured", false)) {
            $returnUrl .= "?laravel-blog-embed=true&laravel-blog-featured=true";
        } else if ($request->get("laravel-blog-embed", false)) {
            $returnUrl .= "?laravel-blog-embed=true";
        } else if ($request->get("laravel-blog-featured", false)) {
            $returnUrl .= "?laravel-blog-featured=true";
        }

        // Return
        return redirect($returnUrl)
            ->with("success", (!$request->get("laravel-blog-embed", false)) ? "Files uploaded successfully!" : '');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BlogFile $file
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy(BlogFile $file)
    {
        if(auth()->user()->cannot("delete", $file)) {
            abort(403);
        }

        $file->delete();

        // Return
        return redirect($this->routePrefix."files")
            ->with("success", "File deleted successfully!");
    }

    /**
     * Uploads a file to the server and creates a DB entry.
     *
     * @param UploadedFile     $file
     * @param BlogFileRequest $request
     * @return string
     * @throws \Exception
     */
    private function uploadFile(UploadedFile $file, $request)
    {
        // Create filename
        $originalFilename = $file->getClientOriginalName();

        $patterns = [
            '@\[date\]@is',
            '@\[datetime\]@is',
            '@\[filename\]@is',
        ];

        $matches = [
            date("Ymd"),
            date("Ymd-His"),
            str_replace(" ", "_", $originalFilename),
        ];

        $filenamePattern = config("laravel-blog.files.filename_format", "[datetime]_[filename]");
        $filename = preg_replace($patterns, $matches, $filenamePattern);

        $caption = $request->caption ? $request->caption[$originalFilename] : '';
        $alt_text = $request->alt_text ? $request->alt_text[$originalFilename] : '';

        $storageLocation = config("laravel-blog.files.storage_location");

        // Create DB record
        BlogFile::create([
            'site_id' => getBlogSiteID(),
            'storage_location' => $storageLocation,
            'path' => $filename,
            'caption' => $caption,
            'alt_text' => $alt_text,
        ]);

        // Upload file
        if ($storageLocation == "public")
        {
            $destinationPath = public_path(config("laravel-blog.files.storage_path"));
        }
        else if($storageLocation == "storage")
        {
            $destinationPath = storage_path("app/public/".config("laravel-blog.files.storage_path"));
        }
        else
        {
            throw new \Exception("files.storage_path has not been properly defined");
        }

        $file->move($destinationPath, $filename);

        return $filename;
    }

    /**
     * Retrieves a request to upload an file from the CKEditor
     *
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function dialogUpload(Request $request)
    {
        $files = request()->file('upload');
        $error_bag = [];
        foreach (is_array($files) ? $files : [$files] as $file)
        {
            $new_filename = $this->uploadFile($file, $request);
        }

        $response = $this->useFile($new_filename);

        return $response;
    }

    /**
     * Automatically populates the URL field on CKEditor after
     * a successful upload.
     *
     * @param $new_filename
     * @return string
     * @throws \Exception
     */
    private function useFile($new_filename)
    {
        if(config("laravel-blog.files.storage_location") == "storage")
        {
            $file = url("storage/".config("laravel-blog.files.storage_path")."/".$new_filename);
        }
        else if(config("laravel-blog.files.storage_location") == "public")
        {
            $file = url(config("laravel-blog.files.storage_path")."/".$new_filename);
        }
        else
        {
            throw new \Exception("files.storage_path has not been properly defined");
        }

        return "<script type='text/javascript'>

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        }

        var funcNum = getUrlParam('CKEditorFuncNum');

        var par = window.parent,
            op = window.opener,
            o = (par && par.CKEDITOR) ? par : ((op && op.CKEDITOR) ? op : false);

        if (op) window.close();
        if (o !== false) o.CKEDITOR.tools.callFunction(funcNum, '$file');
        </script>";
    }
}
