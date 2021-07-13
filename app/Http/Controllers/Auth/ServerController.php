<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Services\Server\ServerQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class ServerController extends Controller
{
    public function beginLogin(Request $request)
    {
        $client_token = 'Inv@lid';
        if($request->has('token')) {
            Log::debug("server.login - Setting server_client_token token for Server-Auth from token");
            $client_token = $request->input('token');
            // Check if out token is MD5 hash, what it should be
            if(!preg_match('/^[a-f0-9]{32}$/', $client_token)) {
                abort(400, 'Invalid request.');
            }
            // Let's store our token for later use
            $request->session()->put('server_client_token', $client_token);
        } elseif ($request->session()->has('server_client_token')) {
            Log::debug("server.login - server_client_token token for Server-Auth already exists - continuing");
            // If we already have token, aka, we are being redirected, then we just use that token.
            $client_token = $request->session()->pull('server_client_token');
        } else {
            Log::debug("server.login - No server_client_token in session and no token in request - Aborting");
            abort(400, 'Invalid request.');
        }

        if (!Auth::check()) {
            Log::debug("server.login - User not logged in - Redirecting to login page");
            return redirect('login');
        }

        $query = New ServerQuery;
        try {
            Log::debug("server.login - Fetching Client IP from Game-Server");
            $query->setUp(config('aurora.gameserver_address'),config('aurora.gameserver_port'),config('aurora.gameserver_auth'));
            $query->runQuery([
                'query' => 'get_auth_client_ip',
                'clienttoken' => $client_token,
            ]);
        } catch (\Exception $e) {
            Log::error("server.login - Error while fetching client IP from Game-Server: ".$e);
            abort(500, $e->getMessage());
        }
        if ($query->response->statuscode != '200') {
            Log::error("server.login - Invalid Status-Code while fetching client IP from Game-Server: ".$query->response->statuscode);
            abort($query->response->statuscode);
        }

        if($request->getClientIp() !== $query->response->data) {
            Log::debug("server.login - Player IP on Server does not match Request IP - Warning User");
            return redirect()->route('server.login.warn');
        }

        Log::debug("server.login - Redirecting to next page");
        return redirect()->route('server.login.end');
    }

    public function warning(Request $request)
    {
        return view('auth.server.warning');
    }

    public function endLogin(Request $request)
    {
        if(!$request->session()->has('server_client_token') || !Auth::check()) {
            Log::debug("server.login - Auth request does not have a server_client_token or User is not logged in - Aborting");
            abort(500, 'Invalid state');
        }

        $client_token = $request->session()->pull('server_client_token');
        
        if($request->user()->byond_key == null) {
            Log::debug("server.login - Unable to Auth - User has no ckey linked");
            return view('auth.server.nokey');
        }
        $query = New ServerQuery;
        try {
            Log::debug("server.login - Sending auth_client request to server for ckey: ".$request->user()->byond_key);
            $query->setUp(config('aurora.gameserver_address'),config('aurora.gameserver_port'),config('aurora.gameserver_auth'));
            $query->runQuery([
                'query' => 'auth_client',
                'clienttoken' => $client_token,
                'key' => $request->user()->byond_key
            ]);
        } catch (\Exception $e) {
            Log::debug("server.login - Error while sending auth_client request to server: ".$e->getMessage());
            abort(500, $e->getMessage());
        }

        if ($query->response->statuscode == '200') {
            Log::debug("server.login - Ckey Succesfully logged in: ".$request->user()->byond_key);
            return view('auth.server.success');
        } else {
            Log::debug("server.login - Invalid status-code while sending auth_client request to server: ".$query->response->statuscode);
            abort(500);
        }
    }
}
