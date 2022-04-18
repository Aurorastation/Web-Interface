<?php

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ServerLog;
use Illuminate\Support\Facades\Log;
use Validator;
use Storage;
use DataTables;

class LogController extends Controller
{

    public function index(Request $request){
        if ($request->user()->cannot('server_logs_show'))
            abort('403', 'You do not have the required permission.');

        $logs = ServerLog::all();
        return view('server.log.index',["logs"=>$logs]);
    }

    public function getLogData(Request $request)
    {
        if ($request->user()->cannot('server_logs_show'))
            abort('403', 'You do not have the required permission.');

        $logs = ServerLog::select(['id', 'logdate', 'gameid']);

        return Datatables::of($logs)
            ->removeColumn('id')
            ->addColumn('action', '<p><a href="{{route(\'server.log.show.get\',[\'log_id\'=>$id])}}" class="btn btn-success" role="button">Download</a></p>')
            ->rawColumns([0])
            ->make();
    }

    public function getShow(Request $request, $log_id){
        if ($request->user()->cannot('server_logs_show'))
            abort('403', 'You do not have the required permission.');

        $logfile = ServerLog::findOrFail($log_id);

        Log::notice('server.log.download - Log Downloaded', ['user_id' => $request->user()->user_id, 'log_id' => $logfile->id]);

        return Storage::disk('server_logs')->download($logfile->filename);
    }

    public function getLogByGameId(Request $request, $game_id){
        if ($request->user()->cannot('server_logs_show'))
            abort('403', 'You do not have the required permission.');

        $logfile = ServerLog::where('gameid',$game_id)->first();
        if(!isset($logfile)){
            abort('404','There is no log file for the requested game id.');
        }

        Log::notice('server.log.download - Log Downloaded', ['user_id' => $request->user()->user_id, 'log_id' => $logfile->id]);

        return Storage::disk('server_logs')->download($logfile->filename);
    }

    public function upload(Request $request){
        $validator = Validator::make($request->all(),[
            'logfile' => ['required','file'],
            'date' => ['required','date'],
            'gameid' => ['required','string','size:8'],
            'key' => [
                'required',
                function ($attribute, $value, $fail) {
                if ($value !== config('aurora.gameserver_logkey')) {
                    $fail($attribute.' is invalid.');
                }
            },]
        ]);

        if($validator->fails()){
            return response([$validator->messages()],400);
        }

        $path = $request->logfile->store('server_logs','server_logs');

        $logdata = new ServerLog();
        $logdata->filename = $path;
        $logdata->gameid = $request->input('gameid');
        $logdata->logdate = $request->input('date');
        $logdata->save();

        Log::notice("server.logfile.upload - New Log Uploaded",["logdata"=>$logdata]);

        return response(null,200);
    }
}
