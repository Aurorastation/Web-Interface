<?php

namespace App\Http\Controllers\Site;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;
use Illuminate\Support\Facades\Log;

use App\Http\Requests;

class LogViewerController extends \Rap2hpoutre\LaravelLogViewer\LogViewerController
{
    public function index()
    {
        if (Request::input('l')) {
            LaravelLogViewer::setFile(base64_decode(Request::input('l')));
        }
        if (Request::input('dl')) {
            Log::notice('perm.log.site.download - SiteLog downloaded', ['user_id' => Request::user()->user_id]);
            return Response::download(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('dl'))));
        } elseif (Request::has('del')) {
            File::delete(LaravelLogViewer::pathToLogFile(base64_decode(Request::input('del'))));
            Log::notice('perm.log.site.delete - SiteLog deleted', ['user_id' => Request::user()->user_id]);
            return Redirect::to(Request::url());
        }
        $logs = LaravelLogViewer::all();

        Log::notice('perm.log.site.view - SiteLog opened', ['user_id' => Request::user()->user_id]);

        return View::make('laravel-log-viewer::log', [
            'logs' => $logs,
            'files' => LaravelLogViewer::getFiles(true),
            'current_file' => LaravelLogViewer::getFileName()
        ]);
    }
}
